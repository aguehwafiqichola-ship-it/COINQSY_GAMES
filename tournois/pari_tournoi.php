<?php
session_start();
include '../bd/bd.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit;
}

$user_id = (int)$_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['tournoi_id']) || !isset($_POST['joueur_id']) || !isset($_POST['montant'])) {
    header("Location: tournoi_detail.php?id=" . ($_POST['tournoi_id'] ?? 0));
    exit;
}

$tournoi_id = (int)$_POST['tournoi_id'];
$joueur_id  = (int)$_POST['joueur_id'];
$montant    = (int)$_POST['montant'];

if ($montant < 10) {
    $_SESSION['error'] = "Le montant minimum est 10 Coinqsy.";
    header("Location: tournoi_detail.php?id=$tournoi_id");
    exit;
}

// Vérifier solde
$stmt = $pdo->prepare("SELECT coins_balance FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

if ($user['coins_balance'] < $montant) {
    $_SESSION['error'] = "Solde insuffisant.";
    header("Location: tournoi_detail.php?id=$tournoi_id");
    exit;
}

// Vérifier tournoi ouvert/en cours
$stmt = $pdo->prepare("SELECT statut FROM tournois WHERE id = ?");
$stmt->execute([$tournoi_id]);
$tournoi = $stmt->fetch();

if (!$tournoi || !in_array($tournoi['statut'], ['ouvert', 'en_cours'])) {
    $_SESSION['error'] = "Les paris sont fermés pour ce tournoi.";
    header("Location: tournoi_detail.php?id=$tournoi_id");
    exit;
}

// Vérifier que le joueur existe dans le tournoi
$stmt = $pdo->prepare("SELECT id FROM tournoi_participations WHERE tournoi_id = ? AND user_id = ?");
$stmt->execute([$tournoi_id, $joueur_id]);
if (!$stmt->fetch()) {
    $_SESSION['error'] = "Ce joueur ne participe pas au tournoi.";
    header("Location: tournois_details.php?id=$tournoi_id");
    exit;
}

// Déduire le montant
$stmt = $pdo->prepare("UPDATE users SET coins_balance = coins_balance - ? WHERE id = ?");
$stmt->execute([$montant, $user_id]);

// Enregistrer le pari
$stmt = $pdo->prepare("
    INSERT INTO paris 
    (user_id, tournoi_id, joueur_parie_sur, montant, cree_le)
    VALUES (?, ?, ?, ?, NOW())
");
$stmt->execute([$user_id, $tournoi_id, $joueur_id, $montant]);

$_SESSION['success'] = "Pari de $montant Coinqsy placé sur ce joueur ! Bonne chance.";
header("Location: tournois_details.php?id=$tournoi_id");
exit;