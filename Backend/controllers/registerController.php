<?php
require_once '../config/auth.php';
require_once '../config/db.php';

welcomy_redirect_if_logged_in();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../../projet_stage/Frontend/register.php');
    exit;
}

$nom = trim($_POST['nom'] ?? '');
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$role = 'hotesse';

if ($nom === '' || $email === '' || $password === '') {
    die('Tous les champs sont requis.');
}

$stmt = $conn->prepare('SELECT id_utilisateur FROM users WHERE email = ?');
$stmt->execute([$email]);
if ($stmt->fetch()) {
    die('Cet email est déjà utilisé.');
}

$hashedPassword = password_hash($password, PASSWORD_BCRYPT);
$stmt = $conn->prepare('INSERT INTO users (nom, email, password, role) VALUES (?, ?, ?, ?)');
$result = $stmt->execute([$nom, $email, $hashedPassword, $role]);

if ($result) {
    header('Location: ../../projet_stage/Frontend/login.php?registered=1');
    exit;
}

echo 'Erreur lors de l\'enregistrement.';
