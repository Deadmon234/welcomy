<?php
require_once '../config/db.php'; // Connexion à la base avec PDO

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nom = $_POST['nom'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $role = 'hotesse';

    if (empty($nom) || empty($email) || empty($password)) {
        die("Tous les champs sont requis.");
    }

    // Vérifier si l'email est déjà utilisé
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        die("Cet email est déjà utilisé.");
    }

    // Hachage du mot de passe
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

    // Insertion dans la table users
    $stmt = $conn->prepare("INSERT INTO users (nom, email, password, role) VALUES (?, ?, ?, ?)");
    $result = $stmt->execute([$nom, $email, $hashedPassword, $role]);

    if ($result) {
        // Rediriger vers la page de login après inscription réussie
        header("Location: ../../projet_stage/Frontend/login.php");
        exit();
    } else {
        echo "Erreur lors de l'enregistrement.";
    }
} else {
    echo "Méthode non autorisée.";
}
