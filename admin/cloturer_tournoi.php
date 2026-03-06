<?php
session_start();

// Protection admin (à renforcer avec un vrai système de rôles)
// if (!isset($_SESSION['user_id']) || $_SESSION['username'] !== 'admin') {
//     die("Accès refusé. Vous n'êtes pas administrateur.");
// }

include '../bd/bd.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("ID du tournoi manquant ou invalide.");
}

$tournoi_id = (int)$_GET['id'];

// 1. Récupérer le tournoi
$stmt = $pdo->prepare("SELECT * FROM tournois WHERE id = ?");
$stmt->execute([$tournoi_id]);
$tournoi = $stmt->fetch();

if (!$tournoi) {
    die("Tournoi introuvable.");
}

if ($tournoi['statut'] !== 'en_cours') {
    die("Ce tournoi n'est pas en cours. Impossible de le clôturer.");
}

// 2. Vérifier qu'il y a au moins un participant avec un rang
$stmt = $pdo->prepare("SELECT COUNT(*) FROM tournoi_participations WHERE tournoi_id = ? AND rank IS NOT NULL");
$stmt->execute([$tournoi_id]);
if ($stmt->fetchColumn() == 0) {
    die("Aucun rang attribué. Veuillez d'abord définir les classements.");
}

// 3. Clôturer le tournoi (statut → 'termine')
$pdo->prepare("UPDATE tournois SET statut = 'termine' WHERE id = ?")
    ->execute([$tournoi_id]);

// 4. Distribuer les prix aux gagnants (exemple simple : 60% au 1er, 30% au 2e, 10% au 3e)
$prix_pool = $tournoi['prix_pool'];

$distribution = [
    1 => 0.60 * $prix_pool,  // 1er
    2 => 0.30 * $prix_pool,  // 2e
    3 => 0.10 * $prix_pool   // 3e
];

$stmt = $pdo->prepare("
    UPDATE tournoi_participations tp
    JOIN users u ON tp.user_id = u.id
    SET tp.coins_gagnes = ?, u.coins_balance = u.coins_balance + ?
    WHERE tp.tournoi_id = ? AND tp.rank = ?
");

foreach ($distribution as $rank => $montant) {
    $montant = (int)$montant;
    $stmt->execute([$montant, $montant, $tournoi_id, $rank]);
}

// 5. Régler les paris (gains pour ceux qui ont parié sur le gagnant)
$stmt = $pdo->prepare("
    SELECT joueur_parie_sur, montant, cote 
    FROM paris 
    WHERE tournoi_id = ? AND statut = 'ouvert'
");
$stmt->execute([$tournoi_id]);
$paris = $stmt->fetchAll();

$gagnant_id = null;
$stmt = $pdo->prepare("SELECT user_id FROM tournoi_participations WHERE tournoi_id = ? AND rank = 1");
$stmt->execute([$tournoi_id]);
if ($row = $stmt->fetch()) {
    $gagnant_id = $row['user_id'];
}

if ($gagnant_id) {
    foreach ($paris as $pari) {
        if ($pari['joueur_parie_sur'] == $gagnant_id) {
            // Gain = montant parié × cote
            $gain = (int)($pari['montant'] * $pari['cote']);

            // Créditer le parieur
            $stmt = $pdo->prepare("UPDATE users SET coins_balance = coins_balance + ? WHERE id = ?");
            $stmt->execute([$gain, $pari['user_id']]);

            // Mettre à jour le pari
            $stmt = $pdo->prepare("UPDATE paris SET statut = 'gagne' WHERE id = ?");
            $stmt->execute([$pari['id']]);
        } else {
            // Perdu
            $stmt = $pdo->prepare("UPDATE paris SET statut = 'perdu' WHERE id = ?");
            $stmt->execute([$pari['id']]);
        }
    }
}

// 6. Message de succès
$_SESSION['success'] = "Tournoi clôturé avec succès ! Prix et paris distribués.";
header("Location: tournois.php");
exit;