<?php
session_start();

// MOT DE PASSE ADMIN (27 caractères)
$ADMIN_PASSWORD = "K9#vP$2mL8&xQ5@eZ3!rT7 jN0";

if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header("Location: conversion_management.php");
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($_POST['admin_password'] === $ADMIN_PASSWORD) {
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['is_admin'] = true;
        header("Location: conversion_management.php");
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
  <title>Connexion Admin - Coinqsy</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-black text-white min-h-screen flex items-center justify-center">
  <div class="bg-gray-900 p-10 rounded-2xl max-w-md w-full border border-red-700">
    <h1 class="text-3xl font-bold text-red-400 text-center mb-8">ACCÈS ADMIN</h1>
    
    <?php if ($error): ?>
      <p class="bg-red-900 text-red-300 p-4 rounded-xl text-center mb-6"><?= $error ?></p>
    <?php endif; ?>

    <form method="POST">
      <input type="password" name="admin_password" placeholder="Mot de passe administrateur" required 
             class="w-full bg-gray-800 border border-gray-700 rounded-xl px-5 py-4 text-center text-lg mb-6">
      <button type="submit" class="w-full bg-red-700 hover:bg-red-600 py-4 rounded-xl font-bold text-xl">
        CONNEXION
      </button>
    </form>
  </div>
</body>
</html>