<?php
session_start();
include '../../bd/bd.php';

if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
    header("Location: login.php");
    exit;
}

// Traitement des actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)($_POST['id'] ?? 0);
    $action = $_POST['action'] ?? '';

    if ($id && $action === 'approve_pack') {
        $pdo->prepare("UPDATE conversion_pack_payments SET status = 'approved', processed_at = NOW() WHERE id = ?")
            ->execute([$id]);
    }
    if ($id && $action === 'approve_conversion') {
        $pdo->prepare("UPDATE conversion_requests SET status = 'approved', processed_at = NOW() WHERE id = ?")
            ->execute([$id]);
    }
}

// Liste des paiements pack en attente
$pack_payments = $pdo->query("
    SELECT p.*, u.username, u.email 
    FROM conversion_pack_payments p 
    JOIN users u ON p.user_id = u.id 
    WHERE p.status = 'pending'
    ORDER BY p.submitted_at DESC
")->fetchAll();

// Liste des demandes de conversion en attente
$conversion_requests = $pdo->query("
    SELECT r.*, u.username, u.email, u.coins_balance 
    FROM conversion_requests r 
    JOIN users u ON r.user_id = u.id 
    WHERE r.status = 'pending'
    ORDER BY r.requested_at DESC
")->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin - Gestion Conversion</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-black text-white p-8 min-h-screen">

  <div class="max-w-7xl mx-auto">
    <h1 class="text-4xl font-bold text-red-400 mb-10 text-center">Administration - Conversion Coinqsy</h1>

    <!-- PAIEMENTS PACK 3000 FCFA -->
    <h2 class="text-2xl font-bold text-yellow-400 mb-6">Paiements Pack Éligibilité (3000 FCFA)</h2>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-16">
      <?php foreach ($pack_payments as $p): ?>
        <div class="bg-gray-900 rounded-2xl p-6 border border-yellow-600">
          <img src="../uploads/proofs/<?= htmlspecialchars($p['proof_image']) ?>" class="w-full h-48 object-cover rounded-xl mb-4">
          <p class="font-bold"><?= htmlspecialchars($p['username']) ?></p>
          <p class="text-sm text-gray-400"><?= htmlspecialchars($p['email']) ?></p>
          <form method="POST" class="mt-6">
            <input type="hidden" name="id" value="<?= $p['id'] ?>">
            <button name="action" value="approve_pack" class="bg-green-600 hover:bg-green-500 px-8 py-3 rounded w-full">
              Approuver le paiement
            </button>
          </form>
        </div>
      <?php endforeach; ?>
    </div>

    <!-- DEMANDES DE CONVERSION -->
    <h2 class="text-2xl font-bold text-cyan-400 mb-6">Demandes de Conversion</h2>
    <div class="overflow-x-auto">
      <table class="w-full bg-gray-900 rounded-2xl">
        <thead>
          <tr class="bg-purple-900">
            <th class="p-4">Utilisateur</th>
            <th class="p-4">Solde</th>
            <th class="p-4">Date demande</th>
            <th class="p-4">Action</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($conversion_requests as $r): ?>
            <tr class="border-t border-gray-700">
              <td class="p-4"><?= htmlspecialchars($r['username']) ?></td>
              <td class="p-4 text-yellow-400"><?= number_format($r['coins_balance']) ?></td>
              <td class="p-4"><?= date('d/m/Y H:i', strtotime($r['requested_at'])) ?></td>
              <td class="p-4">
                <form method="POST">
                  <input type="hidden" name="id" value="<?= $r['id'] ?>">
                  <button name="action" value="approve_conversion" class="bg-green-600 hover:bg-green-500 px-6 py-2 rounded">
                    Approuver & Générer numéro
                  </button>
                </form>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</body>
</html>