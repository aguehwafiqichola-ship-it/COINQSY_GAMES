<?php
session_start();
include '../bd/bd.php';

if (!isset($_SESSION['user_id'])) {
  header("Location: ../auth/login.php");
  exit;
}

$user_id = (int)$_SESSION['user_id'];

// Récupérer infos utilisateur
$stmt = $pdo->prepare("
    SELECT username, email, coins_balance, created_at
    FROM users 
    WHERE id = ?
");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
  die("Utilisateur introuvable.");
}

// Nombre d'invitations
$con = $pdo->prepare("SELECT nombrefois FROM coupons WHERE user_id = ?");
$con->execute([$user_id]);
$conpon = $con->fetch(PDO::FETCH_ASSOC);

// Conditions
$condition1 = $user['coins_balance'] >= 500000;
$condition2 = strtotime($user['created_at']) <= strtotime('-7 days');
$condition3 = ($conpon['nombrefois'] ?? 0) >= 10;

$eligible = $condition1 && $condition2 && $condition3;

// Traitement demande de conversion
$request_sent = false;
$request_message = '';
$email_status = '';

if (isset($_POST['request_conversion']) && $eligible) {
  try {
    // Vérifier pas de demande en attente
    $check = $pdo->prepare("SELECT id FROM conversion_requests WHERE user_id = ? AND status = 'pending'");
    $check->execute([$user_id]);
    if ($check->fetch()) {
      $request_message = "Vous avez déjà une demande en cours.";
    } else {
      // Insérer demande
      $stmt = $pdo->prepare("
                INSERT INTO conversion_requests (user_id, requested_at) 
                VALUES (?, NOW())
            ");
      $stmt->execute([$user_id]);

      // Charger mailer
      require_once __DIR__ . '/mailer.php'; // ← Chemin absolu recommandé

      $admin_email = "aguehwafiqichola@gmail.com";
      $subject = "Nouvelle demande de conversion - " . $user['username'];

      $html = "
                <h2 style='color:#00ff9d;'>Nouvelle demande de conversion</h2>
                <p><strong>Utilisateur :</strong> " . htmlspecialchars($user['username']) . "</p>
                <p><strong>Email :</strong> " . htmlspecialchars($user['email']) . "</p>
                <p><strong>Solde Coinqsy :</strong> " . number_format($user['coins_balance']) . "</p>
                <p><strong>Invités validés :</strong> " . ($conpon['nombrefois'] ?? 0) . " / 10</p>
                <p><strong>Date création compte :</strong> " . date('d/m/Y', strtotime($user['created_at'])) . "</p>
                <br>
                <p style='color:#888;'>Traitez cette demande depuis le panneau admin.</p>
                <p style='color:#888;font-size:12px;'>Coinqsy Games - Support</p>
            ";

      // Envoi réel
      $email = sendMail($admin_email, $subject, $html);

      if ($email) {
        $request_message = "Demande envoyée avec succès !";
        $email_status = "Email envoyé à l'admin avec succès.";
        $request_sent = true;
      } else {
        $request_message = "Demande enregistrée, mais l'email n'est pas parti.";
        $email_status = "Erreur email : " . $email['message'];
      }
    }
  } catch (Exception $e) {
    $request_message = "Erreur système : " . $e->getMessage();
    $email_status = "Exception : " . $e->getMessage();
  }
}
?>
<script src="../assets/js/3.4.17.js"></script>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Document</title>
</head>

<body>

  <div class="bg-gradient-to-br from-gray-900 to-gray-950 rounded-2xl p-8 border border-purple-700/50 shadow-2xl mb-10">
    <h2 class="text-3xl font-bold text-cyan-400 mb-6 text-center">
      Éligibilité à la Conversion
    </h2>

    <p class="text-gray-300 text-center mb-8 max-w-2xl mx-auto">
      Pour convertir tes Coinqsy en FCFA, remplis ces conditions. Une fois validé, tu pourras demander la conversion.
    </p>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-10">
      <div class="bg-gray-800/70 rounded-xl p-6 border <?= $condition1 ? 'border-green-500' : 'border-red-500' ?>">
        <div class="flex items-center gap-4">
          <span class="text-4xl"><?= $condition1 ? '✅' : '❌' ?></span>
          <div>
            <h3 class="text-xl font-bold">500 000 Coinqsy ou plus</h3>
            <p class="text-sm mt-1">Ton solde : <?= number_format($user['coins_balance']) ?> Coinqsy</p>
          </div>
        </div>
      </div>

      <div class="bg-gray-800/70 rounded-xl p-6 border <?= $condition2 ? 'border-green-500' : 'border-red-500' ?>">
        <div class="flex items-center gap-4">
          <span class="text-4xl"><?= $condition2 ? '✅' : '❌' ?></span>
          <div>
            <h3 class="text-xl font-bold">Compte de 7 jours minimum</h3>
            <p class="text-sm mt-1">Inscrit le : <?= date('d/m/Y', strtotime($user['created_at'])) ?></p>
          </div>
        </div>
      </div>

      <div class="bg-gray-800/70 rounded-xl p-6 border <?= $condition3 ? 'border-green-500' : 'border-red-500' ?>">
        <div class="flex items-center gap-4">
          <span class="text-4xl"><?= $condition3 ? '✅' : '❌' ?></span>
          <div>
            <h3 class="text-xl font-bold">Inviter 10 personnes avec ton code</h3>
            <p class="text-sm mt-1">Invités validés : <?= $conpon['nombrefois'] ?? 0 ?> / 10</p>
          </div>
        </div>
      </div>
    </div>

    <?php if ($eligible): ?>
      <?php if ($request_sent): ?>
        <div class="bg-green-900/50 border border-green-500 text-green-300 p-6 rounded-xl text-center">
          <p class="text-xl font-bold mb-2">Demande envoyée !</p>
          <p><?= htmlspecialchars($request_message) ?></p>
          <p class="mt-2 text-sm <?= strpos($email_status, 'succès') !== false ? 'text-green-400' : 'text-red-400' ?>">
            <?= htmlspecialchars($email_status) ?>
          </p>
        </div>
      <?php else: ?>
        <form method="POST" class="text-center">
          <button type="submit" name="request_conversion" class="bg-gradient-to-r from-green-600 to-emerald-700 hover:brightness-110 text-white font-bold text-2xl px-16 py-6 rounded-2xl shadow-2xl transition transform hover:scale-105">
            DEMANDER LA CONVERSION
          </button>
          <p class="text-sm text-gray-400 mt-4">
            Une demande sera envoyée à l'administration.
          </p>
        </form>
      <?php endif; ?>
    <?php else: ?>
      <div class="bg-red-900/40 border border-red-500/50 rounded-xl p-8 text-center">
        <p class="text-xl font-bold mb-4">Non éligible pour l'instant</p>
        <p class="text-gray-300">
          Remplis les 3 conditions ci-dessus pour pouvoir demander la conversion.
        </p>
      </div>
    <?php endif; ?>
  </div>

</body>

</html>