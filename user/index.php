<?php
session_start();

include '../bd/bd.php';

if (!isset($_SESSION['user_id'])) {
  header("Location: ../auth/login.php");
  exit;
}

$user_id = (int)$_SESSION['user_id'];

// 1. Infos joueur
$stmt = $pdo->prepare("SELECT username, coins_balance FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();
$username = $user['username'];
$coins_balance = (int)$user['coins_balance'];

// 2. Nombre total de parties jouées
$stmt = $pdo->prepare("SELECT COUNT(*) FROM game_sessions WHERE user_id = ?");
$stmt->execute([$user_id]);
$games_played = (int)$stmt->fetchColumn();

// 3. Nombre de tournois participés
$stmt = $pdo->prepare("SELECT COUNT(DISTINCT tournament_id) FROM tournament_participations WHERE user_id = ?");
$stmt->execute([$user_id]);
$tournaments_participated = (int)$stmt->fetchColumn();

// 4. Dernières 5 parties
$stmt = $pdo->prepare("
    SELECT game_name, score, coins_earned, played_at 
    FROM game_sessions 
    WHERE user_id = ? 
    ORDER BY played_at DESC 
    LIMIT 5
");
$stmt->execute([$user_id]);
$last_games = $stmt->fetchAll();

// 5. Classements du joueur (top positions sur les jeux joués)
$stmt = $pdo->prepare("
    SELECT 
        gs.game_slug,
        COUNT(*) as games_played,
        MAX(gs.score) as best_score,
        (SELECT COUNT(*) + 1 
         FROM game_scores sub 
         WHERE sub.game_slug = gs.game_slug 
         AND sub.score > MAX(gs.score)) as rank
    FROM game_scores gs
    WHERE gs.user_id = ?
    GROUP BY gs.game_slug
    HAVING games_played > 0
    ORDER BY games_played DESC
    LIMIT 5
");
$stmt->execute([$user_id]);
$player_rankings = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr" class="dark">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Dashboard – Coinqsy Games</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet" />
  <style>
    body {
      font-family: 'Poppins', sans-serif;
      background: #0a0a14;
      color: white;
    }

    .neon-glow {
      text-shadow: 0 0 10px #a855f7, 0 0 20px #00ff9d;
    }
  </style>
</head>

<body class="min-h-screen">

  <!-- HEADER -->
  <header class="fixed top-0 left-0 right-0 z-50 bg-black/85 backdrop-blur-xl border-b border-purple-800/70 shadow-2xl">
    <div class="max-w-7xl mx-auto px-6 flex justify-between items-center h-16">
      <a href="../index.php" class="flex items-center space-x-3">
        <div class="w-10 h-10 bg-gradient-to-br from-purple-600 to-cyan-400 rounded-xl flex items-center justify-center text-2xl font-black">C</div>
        <span class="text-2xl font-bold neon-glow">Coinqsy<span class="text-yellow-400">Games</span></span>
      </a>

      <nav class="hidden md:flex space-x-8">
        <a href="../index.php" class="hover:text-cyan-300 transition">Accueil</a>
        <a href="../jeux/index.php" class="hover:text-cyan-300 transition">Jeux</a>
        <a href="../tournois/index.php" class="hover:text-cyan-300 transition">Tournois</a>
        <a href="dashboard.php" class="text-cyan-400 font-semibold">Dashboard</a>
      </nav>

      <div class="flex items-center space-x-5">
        <span class="text-cyan-300">Bonjour, <?= htmlspecialchars($username) ?></span>
        <a href="../auth/logout.php" class="text-red-400 hover:text-red-300">Déconnexion</a>
      </div>
    </div>
  </header>

  <main class="pt-24 pb-16 max-w-6xl mx-auto px-6">

    <h1 class="text-5xl font-black text-center mb-4 neon-glow">
      Ton Dashboard
    </h1>
    <a href="mes_skins.php">
      <p class="text-xl text-gray-400 text-center mb-12  neon-glow">
        Voir
        Mes Skins
      </p>
    </a>
    <p class="text-xl text-gray-400 text-center mb-12">
      Bienvenue <?= htmlspecialchars($username) ?> • Activité au <?= date('d/m/Y H:i') ?>
    </p>

    <!-- SOLDE COINQSY RÉEL -->
    <div class="bg-gradient-to-br from-purple-900/70 to-indigo-900/70 rounded-3xl p-10 mb-12 text-center border border-purple-600/50 shadow-2xl">
      <div class="text-2xl text-gray-300 mb-3">Ton solde Coinqsy</div>
      <div class="text-7xl md:text-8xl font-black text-yellow-400 neon-glow">
        <?= number_format($coins_balance) ?>
      </div>
      <div class="text-lg text-gray-400 mt-2">Coinqsy</div>

      <?php if ($coins_balance >= 500000): ?>
        <a href="eligibilte.php">
          <div class="mt-6 inline-block bg-green-900/50 border border-green-600 text-green-300 px-6 py-3 rounded-xl">
            Éligible à la conversion en vrai argent ! Contacte-nous pour en savoir plus.
          </div>
        </a>
      <?php else: ?>
        <div class="mt-6 text-gray-400 text-sm">
          Encore <?= number_format(500000 - $coins_balance) ?> Coinqsy pour être éligible
        </div>
      <?php endif; ?>
    </div>

    <!-- Stats principales -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-12">
      <div class="bg-gray-900/70 rounded-2xl p-8 text-center border border-cyan-800/40">
        <div class="text-5xl font-bold text-cyan-400 mb-3"><?= number_format($games_played) ?></div>
        <div class="text-xl">Parties jouées</div>
      </div>
      <div class="bg-gray-900/70 rounded-2xl p-8 text-center border border-purple-800/40">
        <div class="text-5xl font-bold text-purple-400 mb-3"><?= number_format($tournaments_participated) ?></div>
        <div class="text-xl">Tournois participés</div>
      </div>
      <div class="bg-gray-900/70 rounded-2xl p-8 text-center border border-yellow-800/40">
        <div class="text-5xl font-bold text-yellow-400 mb-3"><?= number_format($coins_balance) ?></div>
        <div class="text-xl">Coinqsy gagnés</div>
      </div>
    </div>

    <!-- Dernières parties -->
    <div class="bg-gray-900/60 rounded-2xl p-8 mb-12 border border-purple-700/40">
      <h2 class="text-3xl font-bold mb-6 text-cyan-300">Dernières parties</h2>

      <?php if (empty($last_games)): ?>
        <p class="text-gray-400 text-center py-8">Aucune partie récente. Commence à jouer !</p>
      <?php else: ?>
        <div class="overflow-x-auto">
          <table class="w-full text-left">
            <thead>
              <tr class="border-b border-gray-700">
                <th class="py-4 px-4">Jeu</th>
                <th class="py-4 px-4 text-right">Score</th>
                <th class="py-4 px-4 text-right">Coinqsy gagnés</th>
                <th class="py-4 px-4 text-right">Date</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($last_games as $game): ?>
                <tr class="border-b border-gray-800 hover:bg-gray-800/50">
                  <td class="py-4 px-4"><?= htmlspecialchars($game['game_name']) ?></td>
                  <td class="py-4 px-4 text-right font-medium"><?= number_format($game['score']) ?></td>
                  <td class="py-4 px-4 text-right text-yellow-400 font-medium">+<?= number_format($game['coins_earned']) ?></td>
                  <td class="py-4 px-4 text-right text-gray-400"><?= date('d/m/Y H:i', strtotime($game['played_at'])) ?></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php endif; ?>
    </div>

    <!-- Classements du joueur -->
    <div class="bg-gray-900/60 rounded-2xl p-8 border border-purple-700/40">
      <h2 class="text-3xl font-bold mb-6 text-cyan-300">Tes classements</h2>

      <?php
      // Récupérer les meilleurs classements du joueur
      $stmt = $pdo->prepare("
          SELECT 
              game_slug,
              MAX(score) as best_score,
              (SELECT COUNT(*) + 1 FROM game_scores sub 
               WHERE sub.game_slug = gs.game_slug 
               AND sub.score > MAX(gs.score)) as rank
          FROM game_scores gs
          WHERE gs.user_id = ?
          GROUP BY game_slug
          ORDER BY best_score DESC
          LIMIT 5
      ");
      $stmt->execute([$user_id]);
      $rankings = $stmt->fetchAll();

      if (empty($rankings)): ?>
        <p class="text-gray-400 text-center py-8">Aucun classement pour le moment. Joue plus !</p>
      <?php else: ?>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <?php foreach ($rankings as $r):
            $game_name = ucfirst(str_replace('-', ' ', $r['game_slug']));
          ?>
            <div class="bg-gray-800/50 p-6 rounded-xl border border-purple-600/40">
              <h3 class="text-xl font-bold text-cyan-300 mb-2"><?= htmlspecialchars($game_name) ?></h3>
              <p class="text-lg">Meilleur score : <span class="text-yellow-400 font-bold"><?= number_format($r['best_score']) ?></span></p>
              <p class="text-lg mt-1">Classement : <span class="text-purple-300 font-bold">Top <?= number_format($r['rank']) ?></span></p>
            </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>

    <!-- Actions rapides -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mt-12">
      <a href="../jeux/index.php" class="bg-gradient-to-r from-cyan-600 to-cyan-800 hover:brightness-110 p-10 rounded-2xl text-center text-2xl font-bold transition transform hover:scale-105 block">
        Jouer maintenant →
      </a>
      <a href="../tournois/index.php" class="bg-gradient-to-r from-purple-600 to-pink-700 hover:brightness-110 p-10 rounded-2xl text-center text-2xl font-bold transition transform hover:scale-105 block">
        Rejoindre un tournoi →
      </a>
    </div>
    <!-- SECTION GÉNÉRATION CODE COUPON -->
    <div class="bg-gradient-to-br from-gray-900 to-gray-950 rounded-2xl p-8 border border-purple-700/50 shadow-2xl mb-10">
      <h2 class="text-3xl font-bold text-cyan-400 mb-6 text-center">
        Générateur de Code Coupon
      </h2>

      <p class="text-gray-300 text-center mb-8">
        Format : <code class="bg-gray-800 px-2 py-1 rounded">coingames_{pseudo}_zhi_addplayer_{AAAAMMJJHHMMSS}</code>
      </p>

      <div class="max-w-xl mx-auto">
        <div class="flex flex-col sm:flex-row gap-4 mb-6">
          <input
            type="text"
            id="pseudo-input"
            placeholder="Pseudo du joueur"
            class="flex-1 bg-gray-800 border border-purple-600 rounded-xl px-5 py-4 text-white focus:outline-none focus:border-cyan-400 transition"
            value="<?= htmlspecialchars($_SESSION['username'] ?? '') ?>">
          <button
            id="generate-btn"
            class="bg-gradient-to-r from-cyan-600 to-cyan-800 hover:brightness-110 text-white font-bold px-10 py-4 rounded-xl shadow-lg transition">
            Générer
          </button>
        </div>

        <div class="relative">
          <input
            type="text"
            id="coupon-code"
            readonly
            class="w-full bg-gray-800 border border-purple-600 rounded-xl px-5 py-5 text-xl font-mono text-center text-yellow-400 focus:outline-none cursor-pointer"
            placeholder="Le code apparaîtra ici...">
          <button
            id="copy-btn"
            class="absolute right-3 top-1/2 -translate-y-1/2 bg-purple-700 hover:bg-purple-600 text-white px-5 py-2 rounded-lg text-sm font-medium transition">
            Copier
          </button>
        </div>

        <p id="message" class="text-center mt-4 text-sm min-h-[1.5rem]"></p>
      </div>
    </div>

  </main>

  <footer class="bg-black border-t border-gray-800 py-10 text-center text-gray-500 mt-16">
    <p>© <?= date('Y') ?> Coinqsy Games</p>
  </footer>

<script src="../assets/js/jquery-3.7.1.min.js"></script>
<script>
  document.addEventListener('DOMContentLoaded', () => {
    const pseudoInput = document.getElementById('pseudo-input');
    const generateBtn = document.getElementById('generate-btn');
    const couponCode = document.getElementById('coupon-code');
    const copyBtn = document.getElementById('copy-btn');
    const message = document.getElementById('message');

    generateBtn.addEventListener('click', () => {
      const pseudo = pseudoInput.value.trim();

      if (!pseudo) {
        message.textContent = "Veuillez entrer un pseudo !";
        message.className = "text-red-400 text-center mt-4 text-sm";
        return;
      }

      // Envoi à l'API pour génération + enregistrement
      jQuery.ajax({
        url: 'api/generate_coupon.php',
        type: 'POST',
        contentType: 'application/json',
        data: JSON.stringify({
          pseudo: pseudo
        }),
        success: function(res) {
          if (res.success) {
            couponCode.value = res.code;
            message.textContent = "Code généré et enregistré avec succès !";
            message.className = "text-green-400 text-center mt-4 text-sm";
          } else {
            message.textContent = res.message || "Erreur lors de la génération";
            message.className = "text-red-400 text-center mt-4 text-sm";
          }
        },
        error: function() {
          message.textContent = "Erreur serveur lors de la génération";
          message.className = "text-red-400 text-center mt-4 text-sm";
        }
      });
    });

    copyBtn.addEventListener('click', () => {
      if (!couponCode.value) {
        message.textContent = "Générez un code d'abord !";
        message.className = "text-red-400 text-center mt-4 text-sm";
        return;
      }

      navigator.clipboard.writeText(couponCode.value).then(() => {
        message.textContent = "Code copié dans le presse-papiers !";
        message.className = "text-green-400 text-center mt-4 text-sm";

        copyBtn.textContent = "Copié !";
        copyBtn.classList.add('bg-green-600');
        setTimeout(() => {
          copyBtn.textContent = "Copier";
          copyBtn.classList.remove('bg-green-600');
        }, 2000);
      }).catch(() => {
        message.textContent = "Erreur lors de la copie...";
        message.className = "text-red-400 text-center mt-4 text-sm";
      });
    });
  });
</script>
</body>

</html>