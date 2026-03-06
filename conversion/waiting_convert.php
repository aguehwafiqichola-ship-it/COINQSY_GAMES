<?php
session_start();
if (!isset($_SESSION['conversion_amount'])) {
    header("Location: convert.php");
    exit;
}
$amount = $_SESSION['conversion_amount'];
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Conversion en cours...</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    let timeLeft = 3600; // 1 heure
    const timer = setInterval(() => {
      timeLeft--;
      const min = Math.floor(timeLeft/60);
      const sec = timeLeft % 60;
      document.getElementById('timer').textContent = min + "m " + sec + "s";
      if (timeLeft <= 0) {
        clearInterval(timer);
        document.getElementById('status').innerHTML = `
          <p class="text-green-400 text-2xl">Numéro de retrait prêt !</p>
          <p class="mt-4">Contactez le support :</p>
          <a href="https://wa.me/2290157541851" class="text-green-400 text-3xl">WhatsApp</a>
          <a href="https://t.me/COINQSY_SUPPORT" class="text-blue-400 text-3xl ml-6">Telegram</a>
        `;
      }
    }, 1000);
  </script>
</head>
<body class="bg-black text-white min-h-screen flex items-center justify-center">
  <div class="text-center">
    <h1 class="text-4xl font-bold mb-8">Conversion de <?= number_format($amount) ?> Coinqsy en cours</h1>
    <div id="timer" class="text-7xl font-mono text-yellow-400 mb-8">60m 00s</div>
    <p id="status" class="text-xl">Veuillez patienter 1 heure...</p>
  </div>
</body>
</html>