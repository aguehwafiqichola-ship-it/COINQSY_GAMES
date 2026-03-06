<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit;
}

$user_id = (int)$_SESSION['user_id'];
$username = $_SESSION['username'];

// Connexion DB
try {
    $pdo = new PDO("mysql:host=localhost;dbname=coinqsy_games;charset=utf8mb4", 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur DB");
}

// 1. Skins achetés par cet utilisateur
$stmt = $pdo->prepare("
    SELECT skin_slug, price, purchased_at 
    FROM banque 
    WHERE user_id = ?
    ORDER BY purchased_at DESC
");
$stmt->execute([$user_id]);
$skins = $stmt->fetchAll();

// 2. Statistiques Coinqsy gagnés (par période)
function getCoinsByPeriod($pdo, $user_id, $period) {
    $where = "";
    if ($period === 'day')   $where = "DATE(created_at) = CURDATE()";
    if ($period === 'week')  $where = "YEARWEEK(created_at) = YEARWEEK(CURDATE())";
    if ($period === 'month') $where = "YEAR(created_at) = YEAR(CURDATE()) AND MONTH(created_at) = MONTH(CURDATE())";
    if ($period === 'year')  $where = "YEAR(created_at) = YEAR(CURDATE())";

    $stmt = $pdo->prepare("
        SELECT COALESCE(SUM(coins_balance), 0) as total
        FROM users
        WHERE id = ? AND $where
    ");
    $stmt->execute([$user_id]);
    return (int)$stmt->fetchColumn();
}

$coins_day   = getCoinsByPeriod($pdo, $user_id, 'day');
$coins_week  = getCoinsByPeriod($pdo, $user_id, 'week');
$coins_month = getCoinsByPeriod($pdo, $user_id, 'month');
$coins_year  = getCoinsByPeriod($pdo, $user_id, 'year');
?>

<!DOCTYPE html>
<html lang="fr" class="dark">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Mes Skins – Coinqsy Games</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet"/>
  <style>
    body { font-family: 'Poppins', sans-serif; background: #0a0a14; color: white; }
    .neon-glow { text-shadow: 0 0 10px #a855f7, 0 0 20px #00ff9d; }
  </style>
</head>
<body class="min-h-screen">

  <!-- Header -->
  <header class="fixed top-0 left-0 right-0 z-50 bg-black/85 backdrop-blur-xl border-b border-purple-800/70 shadow-2xl">
    <div class="max-w-7xl mx-auto px-6 flex justify-between items-center h-16">
      <a href="../index.php" class="flex items-center space-x-3">
        <div class="w-10 h-10 bg-gradient-to-br from-purple-600 to-cyan-400 rounded-xl flex items-center justify-center text-2xl font-black">C</div>
        <span class="text-2xl font-bold neon-glow">Coinqsy<span class="text-yellow-400">Games</span></span>
      </a>
      <nav class="hidden md:flex space-x-8">
        <a href="../jeux/" class="hover:text-cyan-300 transition">Jeux</a>
        <a href="index.php" class="text-cyan-400 font-semibold">Dashboard</a>
      </nav>
      <div class="flex items-center space-x-5">
        <span class="text-cyan-300">Bonjour, <?= htmlspecialchars($username) ?></span>
        <a href="../auth/logout.php" class="text-red-400 hover:text-red-300">Déconnexion</a>
      </div>
    </div>
  </header>

  <main class="pt-24 pb-16 max-w-6xl mx-auto px-6">

    <h1 class="text-5xl font-black text-center mb-4 neon-glow">
      Mes Skins
    </h1>

    <p class="text-xl text-gray-400 text-center mb-12">
      Skins achetés et statistiques Coinqsy gagnés
    </p>

    <!-- Skins achetés -->
    <div class="bg-gray-900/70 rounded-2xl p-8 mb-12 border border-purple-700/40">
      <h2 class="text-3xl font-bold mb-6 text-cyan-300">Skins possédés</h2>

      <?php if (empty($skins)): ?>
        <p class="text-gray-400 text-center py-8">Aucun skin acheté pour le moment.</p>
      <?php else: ?>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          <?php foreach ($skins as $skin): ?>
            <div class="bg-gray-800/50 p-6 rounded-xl border border-purple-600/40">
              <h3 class="text-xl font-bold text-cyan-300 mb-2"><?= htmlspecialchars(ucfirst($skin['skin_slug'])) ?></h3>
              <p class="text-gray-400">Prix payé : <span class="text-yellow-400 font-bold"><?= number_format($skin['price']) ?> Coinqsy</span></p>
              <p class="text-gray-400 mt-1">Acheté le : <?= date('d/m/Y H:i', strtotime($skin['purchased_at'])) ?></p>
            </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>

    <!-- Statistiques Coinqsy gagnés -->
    <div class="bg-gray-900/70 rounded-2xl p-8 border border-purple-700/40">
      <h2 class="text-3xl font-bold mb-6 text-cyan-300">Coinqsy gagnés</h2>

      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-gray-800/50 p-6 rounded-xl text-center border border-cyan-800/40">
          <div class="text-4xl font-bold text-cyan-400 mb-2"><?= number_format($coins_day) ?></div>
          <div class="text-lg">Aujourd’hui</div>
        </div>
        <div class="bg-gray-800/50 p-6 rounded-xl text-center border border-purple-800/40">
          <div class="text-4xl font-bold text-purple-400 mb-2"><?= number_format($coins_week) ?></div>
          <div class="text-lg">Cette semaine</div>
        </div>
        <div class="bg-gray-800/50 p-6 rounded-xl text-center border border-yellow-800/40">
          <div class="text-4xl font-bold text-yellow-400 mb-2"><?= number_format($coins_month) ?></div>
          <div class="text-lg">Ce mois</div>
        </div>
        <div class="bg-gray-800/50 p-6 rounded-xl text-center border border-green-800/40">
          <div class="text-4xl font-bold text-green-400 mb-2"><?= number_format($coins_year) ?></div>
          <div class="text-lg">Cette année</div>
        </div>
      </div>
    </div>

    <!-- Bouton retour -->
    <div class="text-center mt-12">
      <a href="index.php" class="inline-block bg-gradient-to-r from-cyan-600 to-cyan-800 hover:brightness-110 px-10 py-5 rounded-xl font-semibold text-lg transition transform hover:scale-105">
        ← Retour au dashboard
      </a>
    </div>

  </main>

  <footer class="bg-black border-t border-gray-800 py-10 text-center text-gray-500 mt-16">
    <p>© <?= date('Y') ?> Coinqsy Games</p>
  </footer>

</body>
</html>