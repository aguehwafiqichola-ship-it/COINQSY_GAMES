<?php
session_start();
include '../bd/bd.php';

if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    header("Location: connexion.php");
    exit;
}

// Traitement action admin
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $request_id = (int)$_POST['request_id'];
    $action = $_POST['action'] ?? '';
    $admin_note = trim($_POST['admin_note'] ?? '');

    if ($request_id && in_array($action, ['approve', 'reject'])) {
        $new_status = $action === 'approve' ? 'approved' : 'rejected';
        $processed_at = date('Y-m-d H:i:s');

        $stmt = $pdo->prepare("
            UPDATE conversion_requests 
            SET status = ?, admin_notes = ?, processed_at = ? 
            WHERE id = ?
        ");
        $stmt->execute([$new_status, $admin_note, $processed_at, $request_id]);

        // Si approuvé → email à l'utilisateur + mise à jour profil
        if ($action === 'approve') {
            // Récupérer infos utilisateur
            $stmt_user = $pdo->prepare("
                SELECT u.email, u.username 
                FROM conversion_requests cr
                JOIN users u ON cr.user_id = u.id
                WHERE cr.id = ?
            ");
            $stmt_user->execute([$request_id]);
            $user = $stmt_user->fetch();

            if ($user) {
                require 'mailer.php';

                $subject = "Votre demande de conversion est approuvée !";
                $html = "
                    <h2 style='color:#00ff9d;'>Félicitations {$user['username']} !</h2>
                    <p>Votre demande de conversion a été approuvée par l'équipe Coinqsy Games.</p>
                    <p>Cliquez ici pour lancer la conversion : 
                       <a href='localhost/COINQSY/conversion/verify.php?token=ABC123' style='color:#a855f7;font-weight:bold;'>Lancer la conversion</a>
                    </p>
                    <p>Le lien est sécurisé et valide 48h.</p>
                    <p style='color:#888;font-size:12px;'>Coinqsy Games - Support</p>
                ";

                sendMail($user['email'], $subject, $html);

                // Option : marquer l'utilisateur comme éligible
                $pdo->prepare("UPDATE users SET eligible_conversion = 1 WHERE id = (SELECT user_id FROM conversion_requests WHERE id = ?)")->execute([$request_id]);
            }
        }

        header("Location: convert_requests.php?success=1");
        exit;
    }
}

// Liste des demandes
$stmt = $pdo->query("
    SELECT cr.id, cr.user_id, cr.requested_at, cr.status, cr.admin_notes, cr.processed_at,
           u.username, u.email, u.coins_balance, u.created_at,
           (SELECT COUNT(*) FROM users) as invited_count
    FROM conversion_requests cr
    JOIN users u ON cr.user_id = u.id
    ORDER BY cr.requested_at DESC
");
$requests = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $pdo->prepare("SELECT nombrefois FROM coupons WHERE user_id = ?");
foreach ($requests as $req) {
    $stmt->execute([$req['user_id']]);
    $coupon = $stmt->fetch(PDO::FETCH_ASSOC);
    $nomb = $coupon ? $coupon['nombrefois'] : 0; 
    }
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin - Demandes de Conversion</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-black text-white min-h-screen p-8">

  <div class="max-w-7xl mx-auto">
    <h1 class="text-4xl font-bold text-cyan-400 mb-10 text-center">
      Gestion des Demandes de Conversion
    </h1>

    <?php if (isset($_GET['success'])): ?>
      <div class="bg-green-900/50 border border-green-500 text-green-300 p-6 rounded-xl text-center mb-8">
        Action effectuée avec succès !
      </div>
    <?php endif; ?>

    <?php if (empty($requests)): ?>
      <p class="text-center text-gray-400 text-xl">Aucune demande pour le moment.</p>
    <?php else: ?>
      <div class="overflow-x-auto">
        <table class="w-full bg-gray-900 rounded-xl border border-purple-700/50">
          <thead>
            <tr class="bg-purple-900/50 text-cyan-300">
              <th class="p-4">ID</th>
              <th class="p-4">Utilisateur</th>
              <th class="p-4">Solde</th>
              <th class="p-4">Invités</th>
              <th class="p-4">Date demande</th>
              <th class="p-4">Statut</th>
              <th class="p-4">Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($requests as $req): ?>
              <tr class="border-t border-purple-800/50 hover:bg-gray-800/50">
                <td class="p-4 text-center"><?= $req['id'] ?></td>
                <td class="p-4">
                  <strong><?= htmlspecialchars($req['username']) ?></strong><br>
                  <small class="text-gray-400"><?= htmlspecialchars($req['email']) ?></small>
                </td>
                <td class="p-4 text-yellow-400 text-center"><?= number_format($req['coins_balance']) ?></td>
                <td class="p-4 text-center"><?= $nomb ?> / 10</td>
                <td class="p-4 text-center"><?= date('d/m/Y H:i', strtotime($req['requested_at'])) ?></td>
                <td class="p-4 text-center">
                  <?php
                  $status = $req['status'];
                  $color = $status === 'pending' ? 'text-yellow-400' : ($status === 'approved' ? 'text-green-400' : 'text-red-400');
                  ?>
                  <span class="<?= $color ?> font-bold"><?= ucfirst($status) ?></span>
                </td>
                <td class="p-4 text-center">
                  <?php if ($req['status'] === 'pending'): ?>
                    <form method="POST" class="inline-block">
                      <input type="hidden" name="request_id" value="<?= $req['id'] ?>">
                      <button name="action" value="approve" class="bg-green-600 hover:bg-green-500 text-white px-4 py-2 rounded mr-2">
                        Approuver
                      </button>
                      <button name="action" value="reject" class="bg-red-600 hover:bg-red-500 text-white px-4 py-2 rounded">
                        Rejeter
                      </button>
                    </form>
                    <form method="POST" class="mt-2">
                      <input type="hidden" name="request_id" value="<?= $req['id'] ?>">
                      <input type="hidden" name="action" value="note">
                      <textarea name="admin_note" placeholder="Note admin (facultatif)" class="w-full bg-gray-800 p-2 rounded text-sm mb-2"></textarea>
                      <button type="submit" class="bg-purple-600 hover:bg-purple-500 text-white px-4 py-2 rounded text-sm">
                        Ajouter note
                      </button>
                    </form>
                  <?php else: ?>
                    <span class="text-gray-500">Traitée le <?= date('d/m/Y H:i', strtotime($req['processed_at'])) ?></span>
                  <?php endif; ?>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php endif; ?>
  </div>
</body>
</html>