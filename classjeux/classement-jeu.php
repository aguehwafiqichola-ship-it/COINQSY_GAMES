<?php
session_start();

// ────────────────────────────────────────────────
// Connexion à la base de données
// ────────────────────────────────────────────────
$host   = 'localhost';
$dbname = 'coinqsy_games';
$dbuser = 'root';          // ← adapte
$dbpass = '';              // ← adapte

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $dbuser, $dbpass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("<div class='text-red-500 text-center p-10 text-2xl'>Erreur de connexion à la base de données</div>");
}

// ────────────────────────────────────────────────
// Récupération du jeu demandé via GET
// ────────────────────────────────────────────────
$game_slug = isset($_GET['game']) ? trim($_GET['game']) : 'coinqsy-rush';

// Noms humains pour l'affichage (tu peux en ajouter)
$game_names = [
    'coinqsy-rush'     => 'Coinqsy Rush',
    'bubble-shooter'   => 'Bubble Shooter Neon',
    'snake-battle'     => 'Neon Snake Battle',
    '2048-fusion'      => '2048 Fusion',
    'pong-multi'       => 'Pong Multi',
    'teaser'           => 'Teaser Classique',   // ← si tu veux garder l'ancien teaser
    // ajoute d'autres jeux ici
];

$display_name = $game_names[$game_slug] ?? ucfirst(str_replace('-', ' ', $game_slug));

// ────────────────────────────────────────────────
// Récupération du classement
// ────────────────────────────────────────────────
$stmt = $pdo->prepare("
    SELECT 
        username,
        score,
        level_reached,
        created_at
    FROM game_scores
    WHERE game_slug = ?
    ORDER BY score DESC, created_at DESC
    LIMIT 50
");

$stmt->execute([$game_slug]);
$rankings = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr" class="dark">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Classement <?= htmlspecialchars($display_name) ?> – Coinqsy Games</title>
  
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800;900&display=swap" rel="stylesheet"/>
  
  <style>
    body {
      font-family: 'Poppins', sans-serif;
      background: #05050f;
      color: white;
    }
    .neon-title {
      background: linear-gradient(90deg, #ffd700, #ffaa00, #ff6b6b, #a855f7, #00ff9d);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
      text-shadow: 0 0 30px rgba(255,215,0,0.6);
    }
  </style>
</head>
<body class="min-h-screen">

  <!-- Header -->
  <header class="fixed top-0 left-0 right-0 z-50 bg-black/80 backdrop-blur-xl border-b border-purple-900/60">
    <div class="max-w-7xl mx-auto px-6 flex justify-between items-center h-16">
      <a href="index.php" class="flex items-center space-x-3">
        <div class="w-10 h-10 bg-gradient-to-br from-yellow-500 to-purple-600 rounded-xl flex items-center justify-center text-2xl font-black">C</div>
        <span class="text-2xl font-extrabold neon-title">Coinqsy Games</span>
      </a>
      <div class="flex items-center space-x-6">
        <a href="../user/index.php" class="hover:text-cyan-400 transition">Dashboard</a>
        <?php if (isset($_SESSION['user_id'])): ?>
          <span class="text-cyan-300">Bonjour, <?= htmlspecialchars($_SESSION['username']) ?></span>
          <a href="../auth/logout.php" class="text-red-400 hover:text-red-300">Déconnexion</a>
        <?php else: ?>
          <a href="../auth/login.php" class="hover:text-cyan-400 transition">Connexion</a>
          <a href="../auth/register.php" class="bg-gradient-to-r from-purple-600 to-cyan-500 px-5 py-2 rounded-lg">Inscription</a>
        <?php endif; ?>
      </div>
    </div>
  </header>

  <!-- Contenu -->
  <main class="pt-24 pb-16 px-4 md:px-8 max-w-6xl mx-auto">

    <h1 class="text-4xl md:text-6xl font-black text-center mb-4 neon-title">
      Classement <?= htmlspecialchars($display_name) ?>
    </h1>

    <p class="text-xl text-gray-400 text-center mb-12">
      Les meilleurs scores enregistrés pour ce jeu
    </p>

    <?php if (empty($rankings)): ?>
      <div class="bg-gray-900/60 rounded-2xl p-12 text-center text-xl text-gray-400 border border-purple-800/50">
        Aucun score enregistré pour l'instant sur ce jeu...<br>
        Sois le premier ! <a href="index.php" class="text-cyan-400 underline">Jouer maintenant</a>
      </div>
    <?php else: ?>
      <div class="bg-gray-900/70 rounded-2xl overflow-hidden border border-purple-700/60 shadow-2xl">
        <table class="w-full text-left border-collapse">
          <thead class="bg-gradient-to-r from-purple-900 to-indigo-950">
            <tr class="text-lg uppercase tracking-wider">
              <th class="px-6 md:px-10 py-5 text-left w-20">#</th>
              <th class="px-6 md:px-10 py-5 text-left">Joueur</th>
              <th class="px-6 md:px-10 py-5 text-right">Score</th>
              <th class="px-6 md:px-10 py-5 text-center">Niv.</th>
              <th class="px-6 md:px-10 py-5 text-right hidden md:table-cell">Date</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-purple-800/40">
            <?php foreach ($rankings as $rank => $row): ?>
              <tr class="hover:bg-purple-950/40 transition-colors <?= $rank === 0 ? 'bg-yellow-900/20' : '' ?>">
                <td class="px-6 md:px-10 py-6 font-bold text-xl text-cyan-400">
                  <?= $rank + 1 ?>
                </td>
                <td class="px-6 md:px-10 py-6 font-medium text-lg">
                  <?= htmlspecialchars($row['username']) ?>
                </td>
                <td class="px-6 md:px-10 py-6 text-right text-2xl font-bold text-yellow-400">
                  <?= number_format($row['score']) ?>
                </td>
                <td class="px-6 md:px-10 py-6 text-center text-xl text-cyan-300">
                  <?= $row['level_reached'] ?>
                </td>
                <td class="px-6 md:px-10 py-6 text-right text-gray-400 hidden md:table-cell">
                  <?= date('d/m/Y H:i', strtotime($row['created_at'])) ?>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php endif; ?>

    <div class="text-center mt-12">
      <a href="../jeux/index.php" class="inline-block bg-gradient-to-r from-purple-600 to-cyan-500 hover:brightness-110 px-10 py-5 rounded-xl font-semibold text-lg transition transform hover:scale-105">
        ← Retour à l'accueil
      </a>
      <a href="../jeux/teaser.php" class="inline-block ml-6 bg-gradient-to-r from-yellow-600 to-amber-600 hover:brightness-110 px-10 py-5 rounded-xl font-semibold text-lg transition transform hover:scale-105">
        → Rejouer à Coinqsy Rush
      </a>
    </div>

  </main>

  <footer class="bg-black/90 border-t border-purple-900/60 py-8 text-center text-gray-500 text-sm mt-12">
    © <?= date('Y') ?> Coinqsy Games – Tous droits réservés
  </footer>

</body>
</html>