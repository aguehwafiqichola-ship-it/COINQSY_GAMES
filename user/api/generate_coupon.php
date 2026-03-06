<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Vous devez être connecté']);
    exit;
}

include '../../bd/bd.php';

$raw = file_get_contents('php://input');
$data = json_decode($raw, true);

if (!$data || !isset($data['pseudo'])) {
    echo json_encode(['success' => false, 'message' => 'Données incomplètes']);
    exit;
}

$user_id = (int)$_SESSION['user_id'];
$pseudo  = trim($data['pseudo']);

// Génération du code
$now = new DateTime();
$dateStr = $now->format('YmdHis');
$code = "coingames_{$pseudo}_zhi_addplayer_{$dateStr}";

try {
    $stmt = $pdo->prepare("
        INSERT INTO coupons 
        (user_id, code, pseudo, type, generated_at)
        VALUES (?, ?, ?, 'inscription', NOW())
    ");
    $stmt->execute([$user_id, $code, $pseudo]);

    echo json_encode([
        'success' => true,
        'code'    => $code,
        'message' => 'Coupon généré et enregistré'
    ]);
} catch (PDOException $e) {
    if ($e->getCode() == 23000) {
        echo json_encode(['success' => false, 'message' => 'Ce code existe déjà (très rare)']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Erreur base de données']);
    }
}