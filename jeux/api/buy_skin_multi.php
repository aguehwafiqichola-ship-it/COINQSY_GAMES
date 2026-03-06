<?php
// api/buy_skin.php

session_start();
header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Vous devez être connecté']);
    exit;
}

$user_id = (int)$_SESSION['user_id'];

try {
    $pdo = new PDO("mysql:host=localhost;dbname=coinqsy_games;charset=utf8mb4", 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Erreur de connexion DB']);
    exit;
}

$raw = file_get_contents('php://input');
$data = json_decode($raw, true);

if (!$data || !isset($data['game_slug']) || !isset($data['skin']) || !isset($data['price'])) {
    echo json_encode(['success' => false, 'message' => 'Données incomplètes']);
    exit;
}

$game_slug = trim($data['game_slug']);
$skin      = trim($data['skin']);
$price     = (int)$data['price'];

if ($price <= 0 || $game_slug === '') {
    echo json_encode(['success' => false, 'message' => 'Paramètres invalides']);
    exit;
}

// Vérifier solde
$stmt = $pdo->prepare("SELECT coins_balance FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

if (!$user || $user['coins_balance'] < $price) {
    echo json_encode(['success' => false, 'message' => 'Solde insuffisant']);
    exit;
}

// Déduire les Coinqsy
$stmt = $pdo->prepare("UPDATE users SET coins_balance = coins_balance - ? WHERE id = ?");
$stmt->execute([$price, $user_id]);

// Enregistrer l'achat spécifique au jeu
$stmt = $pdo->prepare("
    INSERT INTO banque 
    (user_id,username, game_slug, skin_slug, price, purchased_at)
    VALUES (?, ?, ?, ?, ?, NOW())
    ON DUPLICATE KEY UPDATE purchased_at = NOW()
");
$stmt->execute([$user_id, $_SESSION['username'], $game_slug, $skin, $price]);

echo json_encode([
    'success' => true,
    'message' => "Skin '$skin' acheté pour le jeu '$game_slug' ($price Coinqsy)",
    'new_balance' => $user['coins_balance'] - $price
]);