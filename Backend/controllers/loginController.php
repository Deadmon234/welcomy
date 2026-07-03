<?php
session_start();
require_once '../config/db.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        die("Tous les champs sont requis.");
    }

    try {
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id_utilisateur'];
            $_SESSION['nom'] = $user['nom'];
            $_SESSION['role'] = strtolower($user['role']);

            if (strtolower($user['role']) === 'admin') {
                header("Location: ../../projet_stage/Frontend/admin/dashboardOrganisateur.html");
                exit;
            } elseif (strtolower($user['role']) === 'hotesse') {
                header("Location: ../../projet_stage/Frontend/hotesse/dashboardHotesse.php");
                exit;
            } else {
                echo "Rôle inconnu.";
            }
        } else {
            echo "Identifiants incorrects.";
        }

    } catch (PDOException $e) {
        die("Erreur : " . $e->getMessage());
    }
} else {
    echo "Méthode non autorisée.";
}
