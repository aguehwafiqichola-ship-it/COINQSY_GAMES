<?php
session_start();
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *'); // À retirer en production ou limiter aux domaines autorisés

// ────────────────────────────────────────────────
// Configuration base de données
// ────────────────────────────────────────────────
include '../bd/bd.php';

// ────────────────────────────────────────────────
// Seulement POST + JSON
// ────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
    exit;
}

$raw = file_get_contents('php://input');
$data = json_decode($raw, true);

if (json_last_error() !== JSON_ERROR_NONE || !is_array($data)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Données JSON invalides']);
    exit;
}

$score = isset($data['score']) ? (int)$data['score'] : 0;
$level  = isset($data['level'])  ? (int)$data['level']  : 1;

if ($score < 10) {   // score trop bas → on ignore ou seuil minimal
    echo json_encode(['success' => false, 'message' => 'Score trop faible pour être enregistré']);
    exit;
}

// ────────────────────────────────────────────────
// Qui envoie le score ?
// ────────────────────────────────────────────────
$user_id   = null;
$username  = 'Anonyme';

if (!empty($_SESSION['user_id']) && !empty($_SESSION['username'])) {
    $user_id  = (int) $_SESSION['user_id'];
    $username = $_SESSION['username'];
}

// ────────────────────────────────────────────────
// Enregistrement
// ────────────────────────────────────────────────
try {
    $stmt = $pdo->prepare("
        INSERT INTO teaser_scores 
        (user_id, username, score, level_reached, created_at)
        VALUES 
        (:uid, :uname, :score, :level, NOW())
    ");

    $stmt->execute([
        ':uid'    => $user_id,
        ':uname'  => $username,
        ':score'  => $score,
        ':level'  => $level
    ]);

    echo json_encode([
        'success' => true,
        'message' => 'Score enregistré',
        'inserted_id' => $pdo->lastInsertId()
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Erreur lors de l’enregistrement',
        'debug'   => $e->getMessage()   // ← retire en production !
    ]);
}