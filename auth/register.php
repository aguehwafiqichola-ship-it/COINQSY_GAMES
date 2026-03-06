<?php
session_start();
include '../bd/bd.php';

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm  = $_POST['confirm_password'] ?? '';
    $promo    = trim($_POST['promo_code'] ?? '');

    // Validations classiques
    if (empty($username) || strlen($username) < 3) {
        $errors[] = "Le pseudo doit faire au moins 3 caractères.";
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Email invalide.";
    }
    if (strlen($password) < 6) {
        $errors[] = "Le mot de passe doit faire au moins 6 caractères.";
    }
    if ($password !== $confirm) {
        $errors[] = "Les mots de passe ne correspondent pas.";
    }

    // Vérifier si username ou email existe déjà
    if (empty($errors)) {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$username, $email]);
        if ($stmt->fetch()) {
            $errors[] = "Ce pseudo ou cet email est déjà utilisé.";
        }
    }

    // Vérification code promo (si saisi)
    $promo_bonus = 0;
    $promo_message = '';
    if (!empty($promo)) {
        $stmt = $pdo->prepare("
            SELECT id, status, expires_at 
            FROM coupons 
            WHERE code = ? 
              AND status = 'active'
              AND (expires_at IS NULL OR expires_at > NOW())
        ");
        $stmt->execute([$promo]);
        $coupon = $stmt->fetch();

        if ($coupon) {
            $promo_bonus = 100; // Bonus de 100 Coinqsy
            $promo_message = "Code promo valide ! +100 Coinqsy offerts.";
        } else {
            $errors[] = "Code promo invalide, expiré ou déjà utilisé.";
        }
    }

    // Inscription si pas d'erreur
    if (empty($errors)) {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $initial_coins = 0 + $promo_bonus;

        $stmt = $pdo->prepare("
            INSERT INTO users 
            (username, email, password_hash, coins_balance, created_at) 
            VALUES (?, ?, ?, ?, NOW())
        ");
        if ($stmt->execute([$username, $email, $hash, $initial_coins])) {
            $new_user_id = $pdo->lastInsertId();

            // Si code promo utilisé → marquer comme utilisé
            if ($promo_bonus > 0 && isset($coupon['id'])) {
                $stmt_use = $pdo->prepare("
                    UPDATE coupons 
                    SET used_at = NOW(),nombrefois = nombrefois + 1, 
                        used_by_user_id = ?, 
                        status = 'used' 
                    WHERE id = ?
                ");
                $stmt_use->execute([$new_user_id, $coupon['id']]);
            }

            $success = true;
            $success_message = "Inscription réussie !";
            if ($promo_message) $success_message .= " $promo_message";
        } else {
            $errors[] = "Erreur lors de l'inscription. Réessaie.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Inscription - Coinqsy Games</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-purple-950 to-black min-h-screen text-white flex items-center justify-center">

  <div class="bg-gray-900/70 p-10 rounded-2xl border border-purple-600/50 shadow-2xl max-w-md w-full">
    <h1 class="text-4xl font-bold text-center mb-8 text-cyan-400">Inscription</h1>

    <?php if ($success): ?>
      <div class="bg-green-900/50 border border-green-500 text-green-300 p-4 rounded-xl mb-6 text-center">
        <?= htmlspecialchars($success_message) ?><br>
        <a href="login.php" class="underline hover:text-green-200">Connecte-toi maintenant</a>
      </div>
    <?php endif; ?>

    <?php if (!empty($errors)): ?>
      <div class="bg-red-900/50 border border-red-500 text-red-300 p-4 rounded-xl mb-6">
        <?php foreach ($errors as $err): ?>
          <p>・ <?= htmlspecialchars($err) ?></p>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>

    <form method="POST" class="space-y-6">
      <div>
        <label class="block text-gray-300 mb-2">Pseudo</label>
        <input type="text" name="username" required class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-3 focus:outline-none focus:border-cyan-500">
      </div>
      <div>
        <label class="block text-gray-300 mb-2">Email</label>
        <input type="email" name="email" required class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-3 focus:outline-none focus:border-cyan-500">
      </div>
      <div>
        <label class="block text-gray-300 mb-2">Mot de passe</label>
        <input type="password" name="password" required class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-3 focus:outline-none focus:border-cyan-500">
      </div>
      <div>
        <label class="block text-gray-300 mb-2">Confirmer le mot de passe</label>
        <input type="password" name="confirm_password" required class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-3 focus:outline-none focus:border-cyan-500">
      </div>
      <div>
        <label class="block text-gray-300 mb-2">Code promo (si vous en avez)</label>
        <input type="text" name="promo_code" class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-3 focus:outline-none focus:border-cyan-500" placeholder="Ex: coingames_Wafiq_zhi_addplayer_...">
      </div>
      <button type="submit" class="w-full bg-gradient-to-r from-cyan-500 to-purple-600 hover:brightness-110 text-white font-bold py-4 rounded-xl transition">
        S'INSCRIRE
      </button>
    </form>

    <p class="text-center mt-6 text-gray-400">
      Déjà un compte ? <a href="login.php" class="text-cyan-400 hover:underline">Se connecter</a>
    </p>
  </div>

</body>
</html>