<?php
$host = 'localhost';
$db = 'welcomy'; // ← Mets ici le bon nom
$user = 'root';
$pass = ''; // ou ton mot de passe s’il y en a

try {
    $conn = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Connexion échouée : ' . $e->getMessage()
    ]);
    exit;
}
