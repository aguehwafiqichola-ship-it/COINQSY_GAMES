<?php
session_start();
include '../bd/bd.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit;
}

$user_id = (int)$_SESSION['user_id'];

// Tournois ouverts ou en cours
$stmt = $pdo->query("
    SELECT * FROM tournois 
    WHERE statut IN ('ouvert', 'en_cours') 
    ORDER BY date_debut ASC
");
$tournois = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr" class="dark">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Tournois – Coinqsy Games</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet"/>
  <style>
    body { font-family: 'Poppins', sans-serif; background: #0a0a14; color: white; }
    .neon-glow { text-shadow: 0 0 10px #a855f7, 0 0 20px #00ff9d; }
  </style>
</head>
<body class="min-h-screen">

  <header class="fixed top-0 left-0 right-0 z-50 bg-black/85 backdrop-blur-xl border-b border-purple-800/70 shadow-2xl">
    <div class="max-w-7xl mx-auto px-6 flex justify-between items-center h-16">
      <a href="../index.php" class="flex items-center space-x-3">
        <div class="w-10 h-10 bg-gradient-to-br from-purple-600 to-cyan-400 rounded-xl flex items-center justify-center text-2xl font-black">C</div>
        <span class="text-2xl font-bold neon-glow">Coinqsy<span class="text-yellow-400">Games</span></span>
      </a>
      <nav class="hidden md:flex space-x-8">
        <a href="../index.php" class="hover:text-cyan-300 transition">Accueil</a>
        <a href="../jeux/" class="hover:text-cyan-300 transition">Jeux</a>
        <a href="index.php" class="text-cyan-400 font-semibold">Tournois</a>
        <a href="../dashboard.php" class="hover:text-cyan-300 transition">Dashboard</a>
      </nav>
      <div class="flex items-center space-x-5">
        <span class="text-cyan-300">Bonjour, <?= htmlspecialchars($_SESSION['username']) ?></span>
        <a href="../auth/logout.php" class="text-red-400 hover:text-red-300">Déconnexion</a>
      </div>
    </div>
  </header>

  <main class="pt-24 pb-16 max-w-6xl mx-auto px-6">

    <h1 class="text-5xl font-black text-center mb-4 neon-glow">
      Tournois Actifs
    </h1>

    <p class="text-xl text-gray-400 text-center mb-12">
      Rejoins un tournoi, gagne des Coinqsy et monte dans le classement !
    </p>

    <?php if (empty($tournois)): ?>
      <div class="bg-gray-900/70 rounded-2xl p-12 text-center text-xl text-gray-400 border border-purple-800/50">
        Aucun tournoi actif pour le moment...<br>
        Reviens bientôt !
      </div>
    <?php else: ?>
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        <?php foreach ($tournois as $t): ?>
          <div class="bg-gray-900/70 rounded-2xl p-8 border border-purple-700/50 shadow-2xl hover:shadow-purple-600/50 transition-all">
            <h3 class="text-2xl font-bold text-cyan-400 mb-4"><?= htmlspecialchars($t['nom']) ?></h3>
            <p class="text-gray-300 mb-4"><?= nl2br(htmlspecialchars($t['description'])) ?></p>
            
            <div class="space-y-2 text-sm text-gray-400 mb-6">
              <p>Jeu : <span class="text-yellow-400 font-medium"><?= htmlspecialchars($t['jeu_slug']) ?></span></p>
              <p>Début : <span class="font-medium"><?= date('d/m/Y H:i', strtotime($t['date_debut'])) ?></span></p>
              <p>Fin : <span class="font-medium"><?= date('d/m/Y H:i', strtotime($t['date_fin'])) ?></span></p>
              <p>Places : <span class="text-cyan-300"><?= $t['places_restantes'] ?> / <?= $t['places_max'] ?></span></p>
              <p>Frais : <span class="text-yellow-400"><?= $t['frais_inscription'] ?> Coinqsy</span></p>
              <p>Prix pool : <span class="text-green-400 font-bold"><?= number_format($t['prix_pool']) ?> Coinqsy</span></p>
            </div>

            <?php
            $check = $pdo->prepare("SELECT id FROM tournoi_participations WHERE user_id = ? AND tournoi_id = ?");
            $check->execute([$user_id, $t['id']]);
            $inscrit = $check->fetch();
            ?>

            <div class="flex flex-col gap-4 mt-6">
              <?php if ($inscrit): ?>
                <?php if ($t['places_restantes'] == 0 && $t['statut'] === 'ouvert' || $t['statut'] === 'en_cours'): ?>
                  <a href="../jeux/teaser.php?tournoi_id=<?= $t['id'] ?>" 
                     class="w-full bg-gradient-to-r from-green-600 to-green-800 hover:brightness-110 text-white font-bold py-4 rounded-xl text-center transition">
                    Commencer le jeu
                  </a>
                <?php elseif ($t['statut'] === 'en_cours'): ?>
                  <a href="tournois_details.php?id=<?= $t['id'] ?>" 
                     class="w-full bg-gradient-to-r from-cyan-600 to-cyan-800 hover:brightness-110 text-white font-bold py-4 rounded-xl text-center transition">
                    Voir le classement live
                  </a>
                <?php else: ?>
                  <button disabled class="w-full bg-gray-700 text-gray-400 font-bold py-4 rounded-xl cursor-not-allowed">
                    Tournoi fermé
                  </button>
                <?php endif; ?>
              <?php elseif ($t['statut'] === 'ouvert' && $t['places_restantes'] > 0): ?>
                <form action="inscription_tournoi.php" method="POST">
                  <input type="hidden" name="tournoi_id" value="<?= $t['id'] ?>">
                  <button type="submit" class="w-full bg-gradient-to-r from-green-600 to-green-800 hover:brightness-110 text-white font-bold py-4 rounded-xl transition">
                    Participer (<?= $t['frais_inscription'] ?> Coinqsy)
                  </button>
                </form>
              <?php else: ?>
                <button disabled class="w-full bg-gray-700 text-gray-400 font-bold py-4 rounded-xl cursor-not-allowed">
                  Tournoi fermé
                </button>
              <?php endif; ?>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>

  </main>

  <footer class="bg-black border-t border-gray-800 py-10 text-center text-gray-500 mt-16">
    <p>© <?= date('Y') ?> Coinqsy Games</p>
  </footer>

</body>
</html>