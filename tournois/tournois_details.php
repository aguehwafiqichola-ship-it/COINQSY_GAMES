<?php
session_start();
include '../bd/bd.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$tournoi_id = (int)$_GET['id'];

// Infos tournoi
$stmt = $pdo->prepare("SELECT * FROM tournois WHERE id = ?");
$stmt->execute([$tournoi_id]);
$tournoi = $stmt->fetch();

if (!$tournoi) {
    die("Tournoi introuvable.");
}

// Participants + classement
$stmt = $pdo->prepare("
    SELECT u.username, tp.score, tp.rank, tp.coins_gagnes 
    FROM tournoi_participations tp
    JOIN users u ON tp.user_id = u.id
    WHERE tp.tournoi_id = ?
    ORDER BY tp.score DESC, tp.inscrit_le ASC
");
$stmt->execute([$tournoi_id]);
$participants = $stmt->fetchAll();

$total_participants = count($participants);

// Timer
$now = new DateTime();
$fin = new DateTime($tournoi['date_fin']);
$timer_actif = ($tournoi['statut'] === 'en_cours' && $now < $fin);
?>

<!DOCTYPE html>
<html lang="fr" class="dark">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title><?= htmlspecialchars($tournoi['nom']) ?> – Coinqsy Games</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet"/>
  <style>
    body { font-family: 'Poppins', sans-serif; background: #0a0a14; color: white; }
    .neon-glow { text-shadow: 0 0 10px #a855f7, 0 0 20px #00ff9d; }
  </style>
</head>
<body class="min-h-screen">

  <header class="fixed top-0 left-0 right-0 z-50 bg-black/85 backdrop-blur-xl border-b border-purple-800/70">
    <div class="max-w-7xl mx-auto px-6 flex justify-between items-center h-16">
      <a href="../index.php" class="flex items-center space-x-3">
        <div class="w-10 h-10 bg-gradient-to-br from-purple-600 to-cyan-400 rounded-xl flex items-center justify-center text-2xl font-black">C</div>
        <span class="text-2xl font-bold neon-glow">Coinqsy<span class="text-yellow-400">Games</span></span>
      </a>
      <a href="index.php" class="text-cyan-400 hover:underline">← Retour tournois</a>
    </div>
  </header>

  <main class="pt-24 pb-16 max-w-6xl mx-auto px-6">

    <h1 class="text-4xl md:text-5xl font-black text-center mb-4 neon-glow">
      <?= htmlspecialchars($tournoi['nom']) ?>
    </h1>

    <p class="text-xl text-gray-400 text-center mb-10">
      <?= nl2br(htmlspecialchars($tournoi['description'])) ?>
    </p>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-12">
      <div class="bg-gray-900/70 rounded-2xl p-8 text-center border border-purple-700/50">
        <div class="text-2xl text-gray-300 mb-2">Jeu</div>
        <div class="text-3xl font-bold text-cyan-400"><?= htmlspecialchars($tournoi['jeu_slug']) ?></div>
      </div>
      <div class="bg-gray-900/70 rounded-2xl p-8 text-center border border-cyan-700/50">
        <div class="text-2xl text-gray-300 mb-2">Participants</div>
        <div class="text-3xl font-bold text-cyan-400"><?= $total_participants ?> / <?= $tournoi['places_max'] ?></div>
      </div>
      <div class="bg-gray-900/70 rounded-2xl p-8 text-center border border-yellow-700/50">
        <div class="text-2xl text-gray-300 mb-2">Prix pool</div>
        <div class="text-3xl font-bold text-yellow-400"><?= number_format($tournoi['prix_pool']) ?> Coinqsy</div>
      </div>
    </div>

    <?php if ($timer_actif): ?>
      <div class="bg-gradient-to-r from-purple-900/70 to-indigo-900/70 rounded-2xl p-8 mb-12 text-center border border-purple-600/50 shadow-2xl">
        <div class="text-2xl text-gray-300 mb-4">Temps restant</div>
        <div id="timer-live" class="text-5xl md:text-6xl font-black text-cyan-400 neon-glow">
          Calcul en cours...
        </div>
      </div>
    <?php endif; ?>

    <div class="bg-gray-900/70 rounded-2xl p-8 border border-purple-700/40">
      <h2 class="text-3xl font-bold mb-6 text-cyan-300">
        <?= $tournoi['statut'] === 'en_cours' ? 'Classement live' : 'Participants' ?>
      </h2>

      <?php if (empty($participants)): ?>
        <p class="text-gray-400 text-center py-8">Aucun participant pour le moment.</p>
      <?php else: ?>
        <div class="overflow-x-auto">
          <table class="w-full text-left">
            <thead>
              <tr class="border-b border-gray-700">
                <th class="py-4 px-4">Rang</th>
                <th class="py-4 px-4">Joueur</th>
                <th class="py-4 px-4 text-right">Score</th>
                <th class="py-4 px-4 text-right">Coinqsy gagnés</th>
              </tr>
            </thead>
            <tbody id="classement-body">
              <?php foreach ($participants as $index => $p): ?>
                <tr class="border-b border-gray-800 hover:bg-gray-800/50 <?= $index === 0 ? 'bg-yellow-900/20' : '' ?>">
                  <td class="py-4 px-4 font-bold text-cyan-400">
                    <?= $p['rank'] ? $p['rank'] : ($index + 1) ?>
                  </td>
                  <td class="py-4 px-4"><?= htmlspecialchars($p['username']) ?></td>
                  <td class="py-4 px-4 text-right text-yellow-400 font-medium"><?= number_format($p['score']) ?></td>
                  <td class="py-4 px-4 text-right text-green-400 font-medium">+<?= number_format($p['coins_gagnes']) ?></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php endif; ?>
    </div>

  </main>

  <?php if ($timer_actif): ?>
  <script>
    function updateTimer() {
      const fin = new Date('<?= $tournoi['date_fin'] ?>');
      const now = new Date();
      const diff = fin - now;

      if (diff <= 0) {
        document.getElementById('timer-live').innerHTML = 'Tournoi terminé !';
        return;
      }

      const days = Math.floor(diff / (1000 * 60 * 60 * 24));
      const hours = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
      const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
      const seconds = Math.floor((diff % (1000 * 60)) / 1000);

      document.getElementById('timer-live').innerHTML = 
        (days > 0 ? days + 'j ' : '') + 
        hours.toString().padStart(2, '0') + 'h ' +
        minutes.toString().padStart(2, '0') + 'min ' +
        seconds.toString().padStart(2, '0') + 's';
    }

    setInterval(updateTimer, 1000);
    updateTimer();
  </script>
  <?php endif; ?>

  <script>
  // Rafraîchissement classement live (toutes les 8s)
  function refreshClassement() {
    fetch(`tournoi_classement_live.php?id=<?= $tournoi_id ?>`)
      .then(response => response.text())
      .then(html => {
        document.getElementById('classement-body').innerHTML = html;
      })
      .catch(err => console.log('Erreur refresh', err));
  }

  setInterval(refreshClassement, 8000);
  refreshClassement();
  </script>

</body>
</html>