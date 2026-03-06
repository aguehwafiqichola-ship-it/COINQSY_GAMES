<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

include '../../bd/bd.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
    exit;
}

$raw = file_get_contents('php://input');
$data = json_decode($raw, true);

if (!$data || !isset($data['score'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Données incomplètes']);
    exit;
}

$score     = (int)$data['score'];
$game_slug = 'snake';
$vrai_coins = (int)($data['vrai_coins_earned'] ?? floor($score / 100));

$user_id   = $_SESSION['user_id'] ?? null;
$username  = $_SESSION['username'] ?? 'Anonyme';

// 1. Score général (classement)
try {
    $stmt = $pdo->prepare("
        INSERT INTO game_scores 
        (user_id, username, game_slug, score, level_reached, created_at)
        VALUES (?, ?, ?, ?, 1, NOW())
        ON DUPLICATE KEY UPDATE
            score = GREATEST(score, VALUES(score)),
            created_at = NOW()
    ");
    $stmt->execute([$user_id, $username, $game_slug, $score]);
} catch (Exception $e) {
    error_log("Erreur snake score général : " . $e->getMessage());
}

// 2. Mise à jour tournoi en cours (si inscrit)
if ($user_id) {
    $stmt_t = $pdo->prepare("
        SELECT tp.id 
        FROM tournoi_participations tp
        JOIN tournois t ON tp.tournoi_id = t.id
        WHERE tp.user_id = ? 
          AND t.jeu_slug = 'snake'
          AND t.statut = 'en_cours'
        LIMIT 1
    ");
    $stmt_t->execute([$user_id]);
    if ($participation = $stmt_t->fetch()) {
        $stmt_update = $pdo->prepare("
            UPDATE tournoi_participations 
            SET score = GREATEST(score, ?) 
            WHERE id = ?
        ");
        $stmt_update->execute([$score, $participation['id']]);
    }
}

// 3. Ajout vrais Coinqsy
$coins_added = 0;
if ($user_id && $vrai_coins > 0) {
    try {
        $stmt = $pdo->prepare("UPDATE users SET coins_balance = coins_balance + ? WHERE id = ?");
        $stmt->execute([$vrai_coins, $user_id]);
        $coins_added = $vrai_coins;
    } catch (Exception $e) {
        error_log("Erreur ajout Coinqsy : " . $e->getMessage());
    }
}

echo json_encode([
    'success'     => true,
    'message'     => 'Score enregistré',
    'score'       => $score,
    'coins_added' => $coins_added
]);