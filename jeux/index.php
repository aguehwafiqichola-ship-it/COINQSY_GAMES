<?php
session_start();
include '../bd/bd.php';
 if (!isset($_SESSION['user_id'])) {
     header("Location: ../auth/login.php");
     exit;
 }
?>

<!DOCTYPE html>
<html lang="fr" class="dark">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Jeux – Coinqsy Games</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet"/>
  <style>
    body { font-family: 'Poppins', sans-serif; background: #0a0a14; color: white; }
    .neon-glow { text-shadow: 0 0 10px #a855f7, 0 0 20px #00ff9d; }
    .card-hover { transition: all 0.4s ease; }
    .card-hover:hover { transform: scale(1.05); box-shadow: 0 0 30px rgba(168,85,247,0.5); }
  </style>
</head>
<body class="min-h-screen">

  <!-- HEADER (comme index.php) -->
  <header class="fixed top-0 left-0 right-0 z-50 bg-black/85 backdrop-blur-xl border-b border-purple-800/70 shadow-2xl">
    <div class="max-w-7xl mx-auto px-6 flex justify-between items-center h-16">
      <a href="index.php" class="flex items-center space-x-3">
        <div class="w-10 h-10 bg-gradient-to-br from-purple-600 to-cyan-400 rounded-xl flex items-center justify-center text-2xl font-black">C</div>
        <span class="text-2xl font-bold neon-glow">Coinqsy<span class="text-yellow-400">Games</span></span>
      </a>

      <nav class="hidden md:flex space-x-8">
        <a href="../index.php" class="hover:text-cyan-300 transition">Accueil</a>
        <a href="../jeux/index.php" class="text-cyan-400 font-semibold">Jeux</a>
        <a href="../tournois/index.php" class="hover:text-cyan-300 transition">Tournois</a>
        <a href="../user/index.php" class="hover:text-cyan-300 transition">Dashboard</a>
      </nav>

      <div class="flex items-center space-x-5">
        <?php if (isset($_SESSION['user_id'])): ?>
          <span class="text-cyan-300">Bonjour, <?= htmlspecialchars($_SESSION['username']) ?></span>
          <a href="../auth/logout.php" class="text-red-400 hover:text-red-300">Déconnexion</a>
        <?php else: ?>
          <a href="../auth/login.php" class="text-gray-300 hover:text-cyan-400">Connexion</a>
          <a href="../auth/register.php" class="bg-gradient-to-r from-purple-600 to-cyan-500 px-5 py-2 rounded-lg font-semibold">Inscription</a>
        <?php endif; ?>
      </div>
    </div>
  </header>

  <main class="pt-24 pb-16 max-w-7xl mx-auto px-6">

    <h1 class="text-5xl md:text-6xl font-black text-center mb-4 neon-glow">
      Tous les jeux
    </h1>
    <p class="text-xl text-gray-400 text-center mb-12">
      +100 jeux gratuits • Gagne des Coinqsy à chaque partie • Défie les autres joueurs
    </p>

    <!-- Barre de recherche + filtres -->
    <div class="flex flex-col md:flex-row gap-6 mb-12 justify-center">
      <input type="text" placeholder="Rechercher un jeu..." class="bg-gray-800 border border-purple-700 rounded-xl px-6 py-4 w-full md:w-96 focus:outline-none focus:border-cyan-500">
      <select class="bg-gray-800 border border-purple-700 rounded-xl px-6 py-4 focus:outline-none focus:border-cyan-500">
        <option>Tous les jeux</option>
        <option>Arcade</option>
        <option>Puzzle</option>
        <option>Multi-joueurs</option>
        <option>Skill</option>
      </select>
    </div>

    <!-- Grille des jeux -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">

      <!-- Exemple de carte jeu -->
      <div class="bg-gray-900/70 rounded-2xl overflow-hidden border border-purple-800/50 card-hover">
        <div class="h-48 bg-gradient-to-br from-purple-900/70 to-cyan-900/70 relative">
          <div class="absolute inset-0 flex items-center justify-center text-8xl">🫧</div>
          <div class="absolute bottom-3 right-3 bg-yellow-600/80 text-white text-xs px-3 py-1 rounded-full">
            +15 Coinqsy / partie
          </div>
        </div>
        <div class="p-6">
          <h3 class="text-2xl font-bold mb-2">Coinqsy Rush</h3>
          <p class="text-gray-400 mb-4 text-sm"></p>Défie les autres joueurs dans ce battle royale intense</p>
          <div class="flex justify-between items-center">
            <span class="text-cyan-400 font-medium">Arcade</span>
            <a href="teaser.php" class="bg-cyan-600 hover:bg-cyan-500 px-6 py-2 rounded-lg font-semibold transition">
              Jouer
            </a>
          </div>
        </div>
      </div>

      <!-- Autres jeux (copie-colle et adapte) -->
      <div class="bg-gray-900/70 rounded-2xl overflow-hidden border border-purple-800/50 card-hover">
        <div class="h-48 bg-gradient-to-br from-purple-900/70 to-cyan-900/70 flex items-center justify-center text-8xl">🐍</div>
        <div class="p-6">
          <h3 class="text-2xl font-bold mb-2">Coinqsy Snake</h3>
          <p class="text-gray-400 mb-4 text-sm">Mange les points, grandis, évite les collisions</p>
          <div class="flex justify-between items-center">
            <span class="text-cyan-400 font-medium">Arcade</span>
            <a href="snake.php" class="bg-cyan-600 hover:bg-cyan-500 px-6 py-2 rounded-lg font-semibold transition">
              Jouer
            </a>
          </div>
        </div>
      </div>

      <div class="bg-gray-900/70 rounded-2xl overflow-hidden border border-purple-800/50 card-hover">
        <div class="h-48 bg-gradient-to-br from-purple-900/70 to-cyan-900/70 flex items-center justify-center text-8xl">🧩</div>
        <div class="p-6">
          <h3 class="text-2xl font-bold mb-2">2048 Fusion</h3>
          <p class="text-gray-400 mb-4 text-sm">Fusionne les tuiles jusqu'à 8192 et au-delà</p>
          <div class="flex justify-between items-center">
            <span class="text-cyan-400 font-medium">Puzzle</span>
            <a href="#" class="bg-cyan-600 hover:bg-cyan-500 px-6 py-2 rounded-lg font-semibold transition">
              Jouer
            </a>
          </div>
        </div>
      </div>

      <!-- Ajoute 10-20 cartes supplémentaires ici... -->

    </div>

  </main>

  <!-- Footer simple -->
  <footer class="bg-black border-t border-gray-800 py-10 text-center text-gray-500">
    <p>© 2026 Coinqsy Games – Tous droits réservés</p>
    <p class="mt-2 text-sm">Conversion Coinqsy → FCFA : contact via Telegram / WhatsApp (si éligible)</p>
  </footer>

</body>
</html>