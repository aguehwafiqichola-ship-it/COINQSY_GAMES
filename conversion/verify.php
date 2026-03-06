<?php
session_start();
include '../bd/bd.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = (int)$_SESSION['user_id'];
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pseudo = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT id, password_hash FROM users WHERE username = ? AND email = ?");
    $stmt->execute([$pseudo, $email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password_hash'])) {
        $_SESSION['conversion_verified'] = true;
        header("Location: pack.php");
        exit;
    } else {
        $error = "Pseudo, email ou mot de passe incorrect.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Vérification - Conversion Coinqsy</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-black text-white min-h-screen flex items-center justify-center">
  <div class="bg-gray-900 p-10 rounded-2xl max-w-md w-full border border-purple-600">
    <h1 class="text-3xl font-bold text-center mb-8 text-cyan-400">Vérification pour Conversion</h1>

    <?php if ($error): ?>
      <p class="bg-red-900 text-red-300 p-4 rounded-xl text-center mb-6"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <form method="POST">
      <input type="text" name="username" placeholder="Pseudo" required class="w-full bg-gray-800 p-4 rounded-xl mb-4">
      <input type="email" name="email" placeholder="Email" required class="w-full bg-gray-800 p-4 rounded-xl mb-4">
      <input type="password" name="password" placeholder="Mot de passe" required class="w-full bg-gray-800 p-4 rounded-xl mb-6">
      <button type="submit" class="w-full bg-gradient-to-r from-cyan-600 to-purple-600 py-4 rounded-xl font-bold text-xl">
        VÉRIFIER ET CONTINUER
      </button>
    </form>
  </div>
</body>
</html>