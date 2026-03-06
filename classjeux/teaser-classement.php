<?php
session_start();
include '../bd/bd.php';

// ────────────────────────────────────────────────
// Top 20
// ────────────────────────────────────────────────
$stmt = $pdo->query("
    SELECT 
        username,
        score,
        level_reached,
        created_at
    FROM teaser_scores
    ORDER BY score DESC, created_at DESC
    LIMIT 20
");

$rankings = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr" class="dark">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Classement Teaser – Coinqsy Games</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <style>
    body { font-family: 'Poppins', sans-serif; background: #0a0a14; color: white; }
    .neon-text { text-shadow: 0 0 10px #a855f7, 0 0 20px #00ff9d; }
  </style>
</head>
<body class="min-h-screen">

  <div class="max-w-5xl mx-auto px-4 sm:px-6 py-12">

    <header class="text-center mb-12">
      <h1 class="text-5xl md:text-6xl font-black neon-text mb-4">
        Classement Teaser Coinqsy
      </h1>
      <p class="text-xl text-gray-400">
        Les meilleurs scores du mini-jeu « Attrape les Coinqsy »
      </p>
    </header>

    <div class="bg-gray-900/60 backdrop-blur-md rounded-2xl border border-purple-700/40 overflow-hidden shadow-2xl">
      <table class="w-full text-left border-collapse">
        <thead class="bg-gradient-to-r from-purple-900 to-indigo-950">
          <tr class="text-lg uppercase tracking-wider">
            <th class="px-6 py-5 text-left w-16">#</th>
            <th class="px-6 py-5 text-left">Joueur</th>
            <th class="px-6 py-5 text-right">Score</th>
            <th class="px-6 py-5 text-center">Niv.</th>
            <th class="px-6 py-5 text-right hidden sm:table-cell">Date</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-purple-800/30">
          <?php if (empty($rankings)): ?>
            <tr>
              <td colspan="5" class="text-center py-16 text-xl text-gray-400">
                Aucun score enregistré pour le moment...
              </td>
            </tr>
          <?php else: ?>
            <?php foreach ($rankings as $i => $row): ?>
              <tr class="hover:bg-purple-950/40 transition-colors">
                <td class="px-6 py-5 font-bold text-xl text-cyan-400">
                  <?= $i + 1 ?>
                </td>
                <td class="px-6 py-5 font-medium">
                  <?= htmlspecialchars($row['username']) ?>
                </td>
                <td class="px-6 py-5 text-right text-2xl font-bold text-yellow-400">
                  <?= number_format($row['score']) ?>
                </td>
                <td class="px-6 py-5 text-center text-cyan-300">
                  <?= $row['level_reached'] ?>
                </td>
                <td class="px-6 py-5 text-right text-gray-400 hidden sm:table-cell">
                  <?= date('d/m/Y H:i', strtotime($row['created_at'])) ?>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>

    <div class="text-center mt-10">
      <a href="../index.php" class="inline-block bg-gradient-to-r from-purple-600 to-cyan-500 hover:brightness-110 px-10 py-4 rounded-xl font-semibold text-lg transition transform hover:scale-105">
        ← Retour au mini-jeu
      </a>
    </div>

  </div>

</body>
</html>