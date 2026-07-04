<?php
require_once '../config/auth.php';
require_once '../config/db.php';

welcomy_redirect_if_logged_in();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../../projet_stage/Frontend/login.php');
    exit;
}

$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';

if (empty($email) || empty($password)) {
    die('Tous les champs sont requis.');
}

try {
    $stmt = $conn->prepare('SELECT * FROM users WHERE email = ?');
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id_utilisateur'];
        $_SESSION['nom'] = $user['nom'];
        $_SESSION['role'] = strtolower($user['role']);

        header('Location: ' . welcomy_dashboard_url(strtolower($user['role'])));
        exit;
    }

    header('Location: ../../projet_stage/Frontend/login.php?error=1');
    exit;
} catch (PDOException $e) {
    die('Erreur : ' . $e->getMessage());
}
