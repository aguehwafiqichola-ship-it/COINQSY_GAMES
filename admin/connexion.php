<?php
session_start();

require 'env.php';
$ADMIN_PASSWORD = ADMIN_PASSWORD;

// Si déjà connecté en tant qu'admin → redirection
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header("Location: convert_requests.php");
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = $_POST['admin_password'] ?? '';

    if ($password === $ADMIN_PASSWORD) {
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['is_admin'] = true;
        header("Location: conversion_requests.php");
        exit;
    } else {
        $error = "Mot de passe incorrect.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Connexion Admin - Coinqsy Games</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-black via-purple-950 to-gray-950 min-h-screen flex items-center justify-center p-6">

  <div class="bg-gray-900/80 p-10 rounded-2xl border border-red-700/50 shadow-2xl max-w-md w-full">
    <h1 class="text-3xl font-bold text-center mb-8 text-red-400">
      Espace Administration
    </h1>

    <?php if ($error): ?>
      <div class="bg-red-900/50 border border-red-500 text-red-300 p-4 rounded-xl mb-6 text-center">
        <?= htmlspecialchars($error) ?>
      </div>
    <?php endif; ?>

    <form method="POST" class="space-y-6">
      <div>
        <label class="block text-gray-300 mb-2 font-medium">
          Mot de passe administrateur
        </label>
        <input 
          type="password" 
          name="admin_password" 
          required 
          autocomplete="off"
          class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-red-500 focus:ring-2 focus:ring-red-500/50"
          placeholder="Entrez le mot de passe..."
        >
      </div>

      <button 
        type="submit" 
        class="w-full bg-gradient-to-r from-red-700 to-red-900 hover:brightness-110 text-white font-bold py-4 rounded-xl transition shadow-lg"
      >
        CONNEXION ADMIN
      </button>
    </form>

    <p class="text-center mt-6 text-gray-500 text-sm">
      Accès réservé aux administrateurs uniquement
    </p>
  </div>

</body>
</html>