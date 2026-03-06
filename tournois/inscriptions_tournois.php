<?php
session_start();
include '../bd/bd.php'; // ton fichier PDO

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit;
}

$user_id = (int)$_SESSION['user_id'];
$username = $_SESSION['username'] ?? 'Anonyme';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['tournoi_id'])) {
    header("Location: index.php");
    exit;
}

$tournoi_id = (int)$_POST['tournoi_id'];

// 1. Récupérer infos du tournoi
$stmt = $pdo->prepare("
    SELECT * FROM tournois 
    WHERE id = ? AND statut = 'ouvert' AND places_restantes > 0
");
$stmt->execute([$tournoi_id]);
$tournoi = $stmt->fetch();

if (!$tournoi) {
    $_SESSION['error'] = "Ce tournoi n'est plus disponible ou n'existe pas.";
    header("Location: index.php");
    exit;
}

// 2. Vérifier si déjà inscrit
$check = $pdo->prepare("SELECT id FROM tournoi_participations WHERE user_id = ? AND tournoi_id = ?");
$check->execute([$user_id, $tournoi_id]);
if ($check->fetch()) {
    $_SESSION['error'] = "Vous êtes déjà inscrit à ce tournoi.";
    header("Location: index.php");
    exit;
}

// 3. Vérifier solde suffisant
$stmt = $pdo->prepare("SELECT coins_balance FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

if ($user['coins_balance'] < $tournoi['frais_inscription']) {
    $_SESSION['error'] = "Solde insuffisant. Il vous faut " . $tournoi['frais_inscription'] . " Coinqsy.";
    header("Location: index.php");
    exit;
}

// 4. Déduire les frais
$stmt = $pdo->prepare("UPDATE users SET coins_balance = coins_balance - ? WHERE id = ?");
$stmt->execute([$tournoi['frais_inscription'], $user_id]);

// 5. Ajouter la participation
$stmt = $pdo->prepare("
    INSERT INTO tournoi_participations 
    (user_id, tournoi_id, inscrit_le)
    VALUES (?, ?, NOW())
");
$stmt->execute([$user_id, $tournoi_id]);

// 6. Décrémenter les places restantes
$stmt = $pdo->prepare("UPDATE tournois SET places_restantes = places_restantes - 1 WHERE id = ?");
$stmt->execute([$tournoi_id]);

$_SESSION['success'] = "Inscription réussie au tournoi « " . htmlspecialchars($tournoi['nom']) . " » ! Bonne chance !";
header("Location: index.php");
exit;

$stmt = $pdo->prepare("SELECT places_restantes FROM tournois WHERE id = ?");
$stmt->execute([$tournoi_id]);
$places_restantes = $stmt->fetch();
if($places_restantes == 0) {
    $update = $pdo->prepare("UPDATE tournois SET status = 'en_cours' WHERE id = ?");
    $update->execute([$tournoi_id]);
    $_SESSION['error'] = "Le tournoi est maintenant en cours.";
    header("Location: index.php");
    exit;
}