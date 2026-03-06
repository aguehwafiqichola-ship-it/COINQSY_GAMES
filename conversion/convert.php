<?php
session_start();
include '../bd/bd.php';

if (!isset($_SESSION['conversion_verified'])) {
  header("Location: verify.php");
  exit;
}

$user_id = (int)$_SESSION['user_id'];

// Récupérer solde utilisateur
$stmt = $pdo->prepare("SELECT coins_balance FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

if (!$user) {
  die("Utilisateur introuvable");
}

if (isset($_POST['convert'])) {

  $amount = (int)$_POST['amount'];

  if ($amount >= 500000 && $amount <= $user['coins_balance']) {

    // Calcul FCFA (500 000 Coinqsy = 2 000 FCFA)
    $fcfa = ($amount / 500000) * 2000;

    // Vérifier si demande déjà en attente
    $check = $pdo->prepare("SELECT id FROM conversion_requests WHERE user_id = ? AND status = 'pending'");
    $check->execute([$user_id]);

    if ($check->fetch()) {

      $error = "Vous avez déjà une demande de conversion en attente.";

    } else {

      // Enregistrer la demande
      $insert = $pdo->prepare("
      INSERT INTO conversion_send (user_id, coins_amount, fcfa_amount)
      VALUES (?, ?, ?)
      ");

      $insert->execute([
        $user_id,
        $amount,
        $fcfa
      ]);

      $_SESSION['conversion_amount'] = $amount;

      header("Location: waiting_convert.php");
      exit;

    }

  } else {

    $error = "Montant invalide.";

  }

}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
<meta charset="UTF-8">
<title>Conversion Coinqsy</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-black text-white min-h-screen p-8">

<div class="max-w-2xl mx-auto bg-gray-900 p-10 rounded-3xl shadow-2xl">

<h1 class="text-4xl font-bold text-center mb-8">
Demande de Conversion
</h1>

<p class="text-center text-2xl mb-6">
Nom d'utilisateur :
<span class="text-yellow-400">
<?= htmlspecialchars($_SESSION['username'] ?? '') ?>
</span>
</p>

<p class="text-center text-2xl mb-10">
Solde disponible :
<span class="text-yellow-400">
<?= number_format($user['coins_balance']) ?>
</span> Coinqsy
</p>

<?php if (!empty($error)): ?>
<div class="bg-red-600 p-4 rounded-xl mb-6 text-center">
<?= $error ?>
</div>
<?php endif; ?>

<form method="POST">

<label class="block text-xl mb-2">
Montant Coinqsy
</label>

<input
type="number"
name="amount"
id="coins"
min="500000"
placeholder="Minimum 500 000"
class="w-full bg-gray-800 p-5 rounded-2xl text-2xl text-center mb-6"
oninput="convertCoins()"
required
>

<label class="block text-xl mb-2">
Montant en FCFA
</label>

<input
type="text"
id="fcfa"
readonly
class="w-full bg-gray-800 p-5 rounded-2xl text-2xl text-center text-green-400 mb-8"
placeholder="0 FCFA"
>

<p class="text-center text-gray-400 mb-8">
500 000 Coinqsy = 2 000 FCFA
</p>

<button
type="submit"
name="convert"
class="w-full bg-gradient-to-r from-green-600 to-emerald-700 py-6 text-2xl font-bold rounded-2xl hover:scale-105 transition"
>
VALIDER LA CONVERSION
</button>

</form>

</div>

<script>

function convertCoins(){

let coins = document.getElementById("coins").value;

let fcfa = (coins / 500000) * 2000;

if(!coins){
document.getElementById("fcfa").value = "";
return;
}

document.getElementById("fcfa").value = Math.floor(fcfa).toLocaleString() + " FCFA";

}

</script>

</body>
</html>