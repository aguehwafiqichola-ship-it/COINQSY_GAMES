<?php
session_start();
if (!isset($_SESSION['conversion_verified'])) {
  header("Location: verify.php");
  exit;
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Achat Pack Éligibilité - Coinqsy</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-black text-white min-h-screen p-6">
  <div class="max-w-2xl mx-auto bg-gray-900 rounded-3xl p-10">
    <h1 class="text-4xl font-bold text-center mb-8 text-yellow-400">Pack Éligibilité Conversion</h1>
    <p class="text-center text-xl mb-10">Prix : <strong class="text-green-400">3 000 FCFA</strong></p>

    <div class="bg-gray-800 p-8 rounded-2xl mb-10">
      <h3 class="text-2xl font-bold mb-6">Instructions de paiement</h3>
      <p class="mb-4"><strong>Nom :</strong> WAFIQ ICHOLA</p>
      <p class="mb-4"><strong>MTN MoMo :</strong> 01 57 54 18 51</p>
      <p class="mb-4"><strong>
          "Ouvrez MTN MoMo ou composez *185#",<br>
          "Envoyer de l'argent → Numéro → 01 57 54 18 51",<br>
          "Montant : " . 3000 . " FCFA",<br>
          "Validez avec votre code secret",<br>
          "Prenez une photo claire du reçu",<br>
          "Téléchargez-la ci-dessous (très recommandé)",<br>
          "Cliquez sur « J’ai effectué le paiement »"<br>
      </p>
      <p class="mb-8">Après paiement, upload la capture d'écran ci-dessous.</p>
    </div>

    <form action="upload_proof.php" method="POST" enctype="multipart/form-data" class="text-center">
      <input type="file" name="proof" accept="image/*" required class="block mx-auto mb-6 text-sm text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-gradient-to-r from-green-600 to-emerald-700 file:text-white hover:file:bg-gradient-to-r hover:file:from-green-700 hover:file:to-emerald-800" />
      <button type="submit" class="bg-gradient-to-r from-green-600 to-emerald-700 text-white font-bold py-5 px-16 rounded-2xl text-xl">
        J'AI PAYÉ - ENVOYER LA PREUVE
      </button>
    </form>
  </div>
</body>

</html>