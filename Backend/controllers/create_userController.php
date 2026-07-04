<?php
session_start();
header('Content-Type: application/json');
require_once '../config/db.php';

if (!isset($_SESSION['user_id'], $_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['status' => 'error', 'message' => 'Accès refusé.']);
    exit;
}

$nom = trim($_POST['nom'] ?? '');
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$role = strtolower(trim($_POST['role'] ?? ''));

if ($nom === '' || $email === '' || $password === '') {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Tous les champs sont requis.']);
    exit;
}

if (!in_array($role, ['admin', 'hotesse'], true)) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Rôle invalide.']);
    exit;
}

try {
    $check = $conn->prepare("SELECT id_utilisateur FROM users WHERE email = ?");
    $check->execute([$email]);
    if ($check->fetch()) {
        http_response_code(409);
        echo json_encode(['status' => 'error', 'message' => 'Cet email est déjà utilisé.']);
        exit;
    }

    $hashed = password_hash($password, PASSWORD_BCRYPT);
    $stmt = $conn->prepare("INSERT INTO users (nom, email, password, role) VALUES (?, ?, ?, ?)");
    $stmt->execute([$nom, $email, $hashed, $role]);

    echo json_encode([
        'status' => 'success',
        'id_utilisateur' => $conn->lastInsertId(),
        'message' => $role === 'admin' ? 'Administrateur créé.' : 'Hôtesse créée.'
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
