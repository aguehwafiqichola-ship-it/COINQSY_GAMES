<?php
session_start();
include '../bd/bd.php';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($password)) {
        $error = "Veuillez remplir tous les champs.";
    } else {
        $stmt = $pdo->prepare("SELECT id, username, password_hash FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password_hash'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            header("Location:verify.php");
            exit;
        } else {
            $error = "Pseudo ou mot de passe incorrect.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Connexion - Coinqsy Games</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-purple-950 to-black min-h-screen text-white flex items-center justify-center">

  <div class="bg-gray-900/70 p-10 rounded-2xl border border-purple-600/50 shadow-2xl max-w-md w-full">
    <h1 class="text-4xl font-bold text-center mb-8 text-cyan-400">Connexion</h1>

    <?php if ($error): ?>
      <div class="bg-red-900/50 border border-red-500 text-red-300 p-4 rounded-xl mb-6 text-center">
        <?= htmlspecialchars($error) ?>
      </div>
    <?php endif; ?>

    <form method="POST" class="space-y-6">
      <div>
        <label class="block text-gray-300 mb-2">Pseudo</label>
        <input type="text" name="username" required class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-3 focus:outline-none focus:border-cyan-500">
      </div>
      <div>
        <label class="block text-gray-300 mb-2">Mot de passe</label>
        <input type="password" name="password" required class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-3 focus:outline-none focus:border-cyan-500">
      </div>
      <button type="submit" class="w-full bg-gradient-to-r from-cyan-500 to-purple-600 hover:brightness-110 text-white font-bold py-4 rounded-xl transition">
        SE CONNECTER
      </button>
    </form>

    <p class="text-center mt-6 text-gray-400">
      Pas de compte ? <a href="register.php" class="text-cyan-400 hover:underline">S'inscrire</a>
    </p>
  </div>

</body>
</html>