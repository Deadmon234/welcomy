<?php
session_start();
header('Content-Type: application/json');
require_once '../config/db.php';

if (!isset($_SESSION['user_id'], $_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['status' => 'error', 'message' => 'Accès refusé.']);
    exit;
}

$id = isset($_POST['id_even']) ? (int)$_POST['id_even'] : 0;
if ($id <= 0) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Identifiant événement manquant.']);
    exit;
}

try {
    $check = $conn->prepare("SELECT id_even FROM evenements WHERE id_even = ?");
    $check->execute([$id]);
    if (!$check->fetch()) {
        http_response_code(404);
        echo json_encode(['status' => 'error', 'message' => 'Événement introuvable.']);
        exit;
    }

    $conn->beginTransaction();

    $conn->prepare("DELETE FROM presence_verifications WHERE id_even = ?")->execute([$id]);
    $conn->prepare("DELETE FROM liste_invites WHERE id_even = ?")->execute([$id]);
    $conn->prepare("DELETE FROM evenements WHERE id_even = ?")->execute([$id]);

    $conn->commit();

    echo json_encode(['status' => 'success', 'message' => 'Événement supprimé.']);
} catch (PDOException $e) {
    if ($conn->inTransaction()) {
        $conn->rollBack();
    }
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
