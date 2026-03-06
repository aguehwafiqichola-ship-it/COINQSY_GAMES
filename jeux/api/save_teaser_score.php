<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

include '../../bd/bd.php'; // ton fichier de connexion PDO

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
    exit;
}

$raw = file_get_contents('php://input');
$data = json_decode($raw, true);

if (!$data || !isset($data['score']) || !isset($data['game_slug'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Données incomplètes']);
    exit;
}

$score     = (int)$data['score'];
$level     = (int)($data['level'] ?? 1);
$game_slug = trim($data['game_slug']);
$vrai_coins = (int)($data['vrai_coins_earned'] ?? 0);

$user_id   = $_SESSION['user_id'] ?? null;
$username  = $_SESSION['username'] ?? 'Anonyme';

// 1. Enregistrer le score général (classement mondial)
try {
    $stmt = $pdo->prepare("
        INSERT INTO game_scores 
        (user_id, username, game_slug, score, level_reached, created_at)
        VALUES (?, ?, ?, ?, ?, NOW())
        ON DUPLICATE KEY UPDATE
            score = GREATEST(score, VALUES(score)),
            level_reached = GREATEST(level_reached, VALUES(level_reached)),
            created_at = NOW()
    ");
    $stmt->execute([$user_id, $username, $game_slug, $score, $level]);
} catch (PDOException $e) {
    // silencieux
}

// 2. Mise à jour du score dans le tournoi en cours (IMPORTANT)
if ($user_id) {
    $stmt_check = $pdo->prepare("
        SELECT tp.id 
        FROM tournoi_participations tp
        JOIN tournois t ON tp.tournoi_id = t.id
        WHERE tp.user_id = ? 
          AND t.jeu_slug = ?
          AND t.statut = 'en_cours'
        LIMIT 1
    ");
    $stmt_check->execute([$user_id, $game_slug]);
    $participation = $stmt_check->fetch();

    if ($participation) {
        $stmt_update = $pdo->prepare("
            UPDATE tournoi_participations 
            SET score = GREATEST(score, ?),coins_gagnes =  ?  
            WHERE id = ?
        ");
        $stmt_update->execute([$score, $vrai_coins, $participation['id']]);
    }
}

// 3. Ajouter les vrais Coinqsy (si tu veux activer ça)
$coins_added = 0;
if ($user_id && $vrai_coins > 0) {
    try {
        $stmt = $pdo->prepare("UPDATE users SET coins_balance = coins_balance + ? WHERE id = ?");
        $stmt->execute([$vrai_coins, $user_id]);
        $coins_added = $vrai_coins;
    } catch (PDOException $e) {
        // silencieux
    }
}

// Réponse
echo json_encode([
    'success'     => true,
    'message'     => 'Score enregistré',
    'score'       => $score,
    'coins_added' => $coins_added
]);