<?php
session_start();

// Protection admin (à renforcer selon ton système)
// if (!isset($_SESSION['user_id']) || $_SESSION['username'] !== 'admin') {
//     header("Location: ../index.php");
//     exit;
// }

include '../bd/bd.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: tournois.php");
    exit;
}

$tournoi_id = (int)$_GET['id'];

// Récupérer le tournoi
$stmt = $pdo->prepare("SELECT * FROM tournois WHERE id = ?");
$stmt->execute([$tournoi_id]);
$tournoi = $stmt->fetch();

if (!$tournoi) {
    die("Tournoi introuvable.");
}

// Traitement du formulaire de modification
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom           = trim($_POST['nom']);
    $description   = trim($_POST['description']);
    $jeu_slug      = trim($_POST['jeu_slug']);
    $date_debut    = $_POST['date_debut'];
    $date_fin      = $_POST['date_fin'];
    $frais         = (int)$_POST['frais_inscription'];
    $prix_pool     = (int)$_POST['prix_pool'];
    $places_max    = (int)$_POST['places_max'];
    $statut        = $_POST['statut'];

    if ($nom && $jeu_slug && $date_debut && $date_fin && $places_max > 0) {
        $stmt = $pdo->prepare("
            UPDATE tournois SET 
                nom = ?, description = ?, jeu_slug = ?, date_debut = ?, date_fin = ?,
                frais_inscription = ?, prix_pool = ?, places_max = ?, statut = ?
            WHERE id = ?
        ");
        $stmt->execute([
            $nom, $description, $jeu_slug, $date_debut, $date_fin,
            $frais, $prix_pool, $places_max, $statut, $tournoi_id
        ]);

        // Recalculer places_restantes si places_max a changé
        $stmt = $pdo->prepare("UPDATE tournois SET places_restantes = ? WHERE id = ?");
        $stmt->execute([$places_max - $tournoi['places_max'] + $tournoi['places_restantes'], $tournoi_id]);

        $success = "Tournoi modifié avec succès !";
        // Rafraîchir les données
        $stmt = $pdo->prepare("SELECT * FROM tournois WHERE id = ?");
        $stmt->execute([$tournoi_id]);
        $tournoi = $stmt->fetch();
    } else {
        $error = "Tous les champs obligatoires doivent être remplis.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr" class="dark">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Modifier Tournoi – Admin Coinqsy Games</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-950 text-white min-h-screen">

  <header class="bg-black border-b border-purple-800 p-6">
    <div class="max-w-6xl mx-auto flex justify-between items-center">
      <h1 class="text-3xl font-bold text-purple-400">Modifier Tournoi</h1>
      <a href="tournois.php" class="text-cyan-400 hover:underline">← Retour liste</a>
    </div>
  </header>

  <main class="max-w-4xl mx-auto p-6">

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

    <div class="bg-gray-900 rounded-2xl p-8 border border-purple-700/50">
      <form method="POST" class="space-y-6">
        <div>
          <label class="block text-gray-300 mb-2">Nom du tournoi *</label>
          <input type="text" name="nom" value="<?= htmlspecialchars($tournoi['nom']) ?>" required class="w-full bg-gray-800 border border-purple-600 rounded-lg px-4 py-3 focus:outline-none focus:border-cyan-500">
        </div>

        <div>
          <label class="block text-gray-300 mb-2">Description</label>
          <textarea name="description" rows="5" class="w-full bg-gray-800 border border-purple-600 rounded-lg px-4 py-3 focus:outline-none focus:border-cyan-500"><?= htmlspecialchars($tournoi['description']) ?></textarea>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div>
            <label class="block text-gray-300 mb-2">Jeu *</label>
            <select name="jeu_slug" required class="w-full bg-gray-800 border border-purple-600 rounded-lg px-4 py-3">
              <option value="coinqsy-rush" <?= $tournoi['jeu_slug'] === 'coinqsy-rush' ? 'selected' : '' ?>>Coinqsy Rush</option>
              <option value="teaser" <?= $tournoi['jeu_slug'] === 'teaser' ? 'selected' : '' ?>>Teaser Classique</option>
              <!-- Ajoute d'autres jeux ici -->
            </select>
          </div>

          <div>
            <label class="block text-gray-300 mb-2">Statut *</label>
            <select name="statut" required class="w-full bg-gray-800 border border-purple-600 rounded-lg px-4 py-3">
              <option value="ouvert"   <?= $tournoi['statut'] === 'ouvert'   ? 'selected' : '' ?>>Ouvert</option>
              <option value="en_cours" <?= $tournoi['statut'] === 'en_cours' ? 'selected' : '' ?>>En cours</option>
              <option value="termine"  <?= $tournoi['statut'] === 'termine'  ? 'selected' : '' ?>>Terminé</option>
              <option value="annule"   <?= $tournoi['statut'] === 'annule'   ? 'selected' : '' ?>>Annulé</option>
            </select>
          </div>

          <div>
            <label class="block text-gray-300 mb-2">Places max *</label>
            <input type="number" name="places_max" value="<?= $tournoi['places_max'] ?>" required min="2" class="w-full bg-gray-800 border border-purple-600 rounded-lg px-4 py-3">
          </div>

          <div>
            <label class="block text-gray-300 mb-2">Frais d’inscription</label>
            <input type="number" name="frais_inscription" value="<?= $tournoi['frais_inscription'] ?>" min="0" class="w-full bg-gray-800 border border-purple-600 rounded-lg px-4 py-3">
          </div>

          <div>
            <label class="block text-gray-300 mb-2">Prix pool total</label>
            <input type="number" name="prix_pool" value="<?= $tournoi['prix_pool'] ?>" min="0" class="w-full bg-gray-800 border border-purple-600 rounded-lg px-4 py-3">
          </div>

          <div>
            <label class="block text-gray-300 mb-2">Date de début *</label>
            <input type="datetime-local" name="date_debut" value="<?= str_replace(' ', 'T', $tournoi['date_debut']) ?>" required class="w-full bg-gray-800 border border-purple-600 rounded-lg px-4 py-3">
          </div>

          <div>
            <label class="block text-gray-300 mb-2">Date de fin *</label>
            <input type="datetime-local" name="date_fin" value="<?= str_replace(' ', 'T', $tournoi['date_fin']) ?>" required class="w-full bg-gray-800 border border-purple-600 rounded-lg px-4 py-3">
          </div>
        </div>

        <button type="submit" class="w-full bg-gradient-to-r from-cyan-600 to-cyan-800 hover:brightness-110 text-white font-bold py-4 rounded-xl text-lg mt-6">
          Enregistrer les modifications
        </button>
      </form>
    </div>

  </main>

</body>
</html>