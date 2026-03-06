<?php
session_start();
include '../bd/bd.php';

if (!isset($_SESSION['conversion_verified'])) {
    header("Location: verify.php");
    exit;
}

$user_id = (int)$_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['proof'])) {
    $file = $_FILES['proof'];
    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = "proof_" . $user_id . "_" . time() . "." . $ext;
    $path = "uploads/proofs/" . $filename;

    if (move_uploaded_file($file['tmp_name'], $path)) {
        $stmt = $pdo->prepare("
            INSERT INTO conversion_pack_payments (user_id, proof_image, status) 
            VALUES (?, ?, 'pending')
        ");
        $stmt->execute([$user_id, $filename]);

        $_SESSION['proof_uploaded'] = true;
        header("Location: waiting_pack.php");
        exit;
    }
}