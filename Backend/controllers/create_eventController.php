<?php
session_start();
header('Content-Type: application/json');
require_once '../config/db.php';

if (!isset($_SESSION['user_id'], $_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['status' => 'error', 'message' => 'Accès refusé.']);
    exit;
}

$title = trim($_POST['title'] ?? '');
$date = trim($_POST['date'] ?? '');
$location = trim($_POST['location'] ?? '');
$description = trim($_POST['description'] ?? '');

if ($title === '' || $location === '') {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Titre et lieu sont requis.']);
    exit;
}

if ($date === '') {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'La date de l\'événement est requise.']);
    exit;
}

$date = str_replace('T', ' ', $date);
if (strlen($date) === 16) {
    $date .= ':00';
}

try {
    $conn->exec("CREATE TABLE IF NOT EXISTS evenements (
        id_even BIGINT NOT NULL AUTO_INCREMENT,
        id_utilisateur BIGINT NOT NULL,
        nom VARCHAR(255) NOT NULL,
        lieu VARCHAR(255) NOT NULL,
        date_ DATETIME NOT NULL,
        description VARCHAR(255) DEFAULT NULL,
        PRIMARY KEY (id_even)
    ) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;");

    $stmt = $conn->prepare("INSERT INTO evenements (id_utilisateur, nom, lieu, date_, description) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([
        $_SESSION['user_id'],
        $title,
        $location,
        $date,
        $description ?: null
    ]);
    $id = $conn->lastInsertId();

    echo json_encode(['status' => 'success', 'id_even' => $id]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}

