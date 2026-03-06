<?php
session_start();
if (!isset($_SESSION['proof_uploaded'])) {
    header("Location: pack.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Confirmation en cours - Coinqsy</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    let timeLeft = 1800; // 30 minutes
    const timer = setInterval(() => {
      timeLeft--;
      document.getElementById('timer').textContent = 
        Math.floor(timeLeft/60) + "m " + (timeLeft%60) + "s";
      if (timeLeft <= 0) {
        clearInterval(timer);
        document.getElementById('status').innerHTML = 
          "Confirmation terminée ! Redirection...";
        setTimeout(() => window.location = 'convert.php', 2000);
      }
    }, 1000);
  </script>
</head>
<body class="bg-black text-white min-h-screen flex items-center justify-center">
  <div class="text-center">
    <h1 class="text-4xl font-bold mb-8">Vérification du paiement...</h1>
    <p class="text-xl mb-6">Ne fermez pas cette page</p>
    <div id="timer" class="text-6xl font-mono text-yellow-400">30m 00s</div>
    <p id="status" class="mt-10 text-green-400">Paiement en cours de confirmation par l'admin</p>
  </div>
</body>
</html>