<?php
session_start();

// Protection simple (à renforcer avec rôle admin ou login admin)
// if (!isset($_SESSION['user_id']) || $_SESSION['user_id'] !== 9) {
//     header("Location: ../auth/login.php");
//     exit;
// }

include '../bd/bd.php';

// Création d’un nouveau tournoi
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'create') {
    $nom           = trim($_POST['nom']);
    $description   = trim($_POST['description']);
    $jeu_slug      = trim($_POST['jeu_slug']);
    $date_debut    = $_POST['date_debut'];
    $date_fin      = $_POST['date_fin'];
    $frais         = (int)$_POST['frais_inscription'];
    $prix_pool     = (int)$_POST['prix_pool'];
    $places_max    = (int)$_POST['places_max'];

    if ($nom && $jeu_slug && $date_debut && $date_fin && $places_max > 0) {
        $stmt = $pdo->prepare("
            INSERT INTO tournois 
            (nom, description, jeu_slug, date_debut, date_fin, frais_inscription, prix_pool, places_max, places_restantes)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([$nom, $description, $jeu_slug, $date_debut, $date_fin, $frais, $prix_pool, $places_max, $places_max]);
        $success = "Tournoi créé avec succès !";
    } else {
        $error = "Tous les champs obligatoires doivent être remplis.";
    }
}

// Liste des tournois
$stmt = $pdo->query("SELECT * FROM tournois ORDER BY date_debut DESC");
$tournois = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr" class="dark">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Admin – Gestion Tournois | Coinqsy Games</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet"/>
</head>
<body class="bg-gray-950 text-white min-h-screen">

  <header class="bg-black border-b border-purple-800 p-6">
    <div class="max-w-6xl mx-auto flex justify-between items-center">
      <h1 class="text-3xl font-bold text-purple-400">Admin – Tournois</h1>
      <a href="../dashboard.php" class="text-cyan-400 hover:underline">Retour Dashboard</a>
    </div>
  </header>

  <main class="max-w-6xl mx-auto p-6">

    <?php if (isset($success)): ?>
      <div class="bg-green-900/50 border border-green-600 text-green-300 p-6 rounded-xl mb-8">
        <?= htmlspecialchars($success) ?>
      </div>
    <?php endif; ?>
    <?php if (isset($error)): ?>
      <div class="bg-red-900/50 border border-red-600 text-red-300 p-6 rounded-xl mb-8">
        <?= htmlspecialchars($error) ?>
      </div>
    <?php endif; ?>

    <!-- Formulaire création tournoi -->
    <div class="bg-gray-900 rounded-2xl p-8 mb-12 border border-purple-700/50">
      <h2 class="text-3xl font-bold mb-6 text-cyan-400">Créer un nouveau tournoi</h2>
      <form method="POST" class="space-y-6">
        <input type="hidden" name="action" value="create">

        <div>
          <label class="block text-gray-300 mb-2">Nom du tournoi *</label>
          <input type="text" name="nom" required class="w-full bg-gray-800 border border-purple-600 rounded-lg px-4 py-3 focus:outline-none focus:border-cyan-500">
        </div>

        <div>
          <label class="block text-gray-300 mb-2">Description</label>
          <textarea name="description" rows="4" class="w-full bg-gray-800 border border-purple-600 rounded-lg px-4 py-3 focus:outline-none focus:border-cyan-500"></textarea>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div>
            <label class="block text-gray-300 mb-2">Jeu *</label>
            <select name="jeu_slug" required class="w-full bg-gray-800 border border-purple-600 rounded-lg px-4 py-3">
              <option value="coinqsy-rush">Coinqsy Rush</option>
              <option value="teaser">Teaser Classique</option>
              <option value="bubble-shooter">Bubble Shooter</option>
              <!-- Ajoute d’autres jeux ici -->
            </select>
          </div>

          <div>
            <label class="block text-gray-300 mb-2">Places max *</label>
            <input type="number" name="places_max" required min="2" value="100" class="w-full bg-gray-800 border border-purple-600 rounded-lg px-4 py-3">
          </div>

          <div>
            <label class="block text-gray-300 mb-2">Date de début *</label>
            <input type="datetime-local" name="date_debut" required class="w-full bg-gray-800 border border-purple-600 rounded-lg px-4 py-3">
          </div>

          <div>
            <label class="block text-gray-300 mb-2">Date de fin *</label>
            <input type="datetime-local" name="date_fin" required class="w-full bg-gray-800 border border-purple-600 rounded-lg px-4 py-3">
          </div>

          <div>
            <label class="block text-gray-300 mb-2">Frais d’inscription (Coinqsy)</label>
            <input type="number" name="frais_inscription" min="0" value="0" class="w-full bg-gray-800 border border-purple-600 rounded-lg px-4 py-3">
          </div>

          <div>
            <label class="block text-gray-300 mb-2">Prix pool total (Coinqsy)</label>
            <input type="number" name="prix_pool" min="0" value="0" class="w-full bg-gray-800 border border-purple-600 rounded-lg px-4 py-3">
          </div>
        </div>

        <button type="submit" class="w-full bg-gradient-to-r from-cyan-600 to-cyan-800 hover:brightness-110 text-white font-bold py-4 rounded-xl text-lg mt-6">
          Créer le tournoi
        </button>
      </form>
    </div>

    <!-- Liste des tournois existants -->
    <div class="bg-gray-900 rounded-2xl p-8 border border-purple-700/50">
      <h2 class="text-3xl font-bold mb-6 text-cyan-400">Tournois existants</h2>

      <?php if (empty($tournois)): ?>
        <p class="text-gray-400 text-center py-8">Aucun tournoi créé pour le moment.</p>
      <?php else: ?>
        <div class="overflow-x-auto">
          <table class="w-full text-left">
            <thead>
              <tr class="border-b border-gray-700">
                <th class="py-4 px-4">Nom</th>
                <th class="py-4 px-4">Jeu</th>
                <th class="py-4 px-4">Début</th>
                <th class="py-4 px-4">Fin</th>
                <th class="py-4 px-4">Places</th>
                <th class="py-4 px-4">Statut</th>
                <th class="py-4 px-4 col-span-2">Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($tournois as $t): ?>
                <tr class="border-b border-gray-800 hover:bg-gray-800/50">
                  <td class="py-4 px-4 font-medium"><?= htmlspecialchars($t['nom']) ?></td>
                  <td class="py-4 px-4"><?= htmlspecialchars($t['jeu_slug']) ?></td>
                  <td class="py-4 px-4"><?= date('d/m/Y H:i', strtotime($t['date_debut'])) ?></td>
                  <td class="py-4 px-4"><?= date('d/m/Y H:i', strtotime($t['date_fin'])) ?></td>
                  <td class="py-4 px-4"><?= $t['places_restantes'] ?> / <?= $t['places_max'] ?></td>
                  <td class="py-4 px-4">
                    <span class="px-3 py-1 rounded-full text-sm <?= $t['statut'] === 'ouvert' ? 'bg-green-600' : ($t['statut'] === 'en_cours' ? 'bg-yellow-600' : 'bg-gray-600') ?>">
                      <?= ucfirst($t['statut']) ?>
                    </span>
                  </td>
                  <td class="py-4 px-4">
                    <a href="edit.php?id=<?= $t['id'] ?>" class="text-cyan-400 hover:underline">Modifier</a>
                    <a href="cloturer_tournoi.php?id=<?= $t['id'] ?>" class="text-red-400 hover:underline ml-4">Clôturer</a>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php endif; ?>
    </div>

  </main>

  <footer class="bg-black border-t border-gray-800 py-10 text-center text-gray-500 mt-16">
    <p>© <?= date('Y') ?> Coinqsy Games – Admin</p>
  </footer>

</body>
</html>