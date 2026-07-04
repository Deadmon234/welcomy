<?php
session_start();
header('Content-Type: application/json');
require_once '../config/db.php';

if (!isset($_SESSION['user_id'], $_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['status' => 'error', 'message' => 'Accès refusé.']);
    exit;
}

$id = isset($_POST['id_utilisateur']) ? (int)$_POST['id_utilisateur'] : 0;
if ($id <= 0) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Identifiant utilisateur manquant.']);
    exit;
}

if ($id === (int)$_SESSION['user_id']) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Vous ne pouvez pas supprimer votre propre compte.']);
    exit;
}

try {
    $userStmt = $conn->prepare("SELECT id_utilisateur, role FROM users WHERE id_utilisateur = ?");
    $userStmt->execute([$id]);
    $user = $userStmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        http_response_code(404);
        echo json_encode(['status' => 'error', 'message' => 'Utilisateur introuvable.']);
        exit;
    }

    if ($user['role'] === 'admin') {
        $countStmt = $conn->query("SELECT COUNT(*) FROM users WHERE role = 'admin'");
        if ((int)$countStmt->fetchColumn() <= 1) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Impossible de supprimer le dernier administrateur.']);
            exit;
        }
    }

    $del = $conn->prepare("DELETE FROM users WHERE id_utilisateur = ?");
    $del->execute([$id]);

    echo json_encode(['status' => 'success', 'message' => 'Utilisateur supprimé.']);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
