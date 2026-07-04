<?php
session_start();
header('Content-Type: application/json');
require_once '../config/db.php';

if (!isset($_SESSION['user_id'], $_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['status' => 'error', 'message' => 'Accès refusé.']);
    exit;
}

try {
    $column = $conn->query("SHOW COLUMNS FROM liste_invites LIKE 'id_utilisateur'")->fetch(PDO::FETCH_ASSOC);
    if (!$column) {
        $conn->exec("ALTER TABLE liste_invites ADD COLUMN id_utilisateur BIGINT NULL AFTER id_liste_invite");
    }

    $stmt = $conn->prepare("SELECT u.id_utilisateur, u.nom, u.email,
        COUNT(li.id_liste_invite) AS total_invites,
        SUM(CASE WHEN li.est_present = 1 THEN 1 ELSE 0 END) AS total_present
        FROM users u
        LEFT JOIN liste_invites li ON li.id_utilisateur = u.id_utilisateur
        WHERE u.role = 'hotesse'
        GROUP BY u.id_utilisateur, u.nom, u.email
        ORDER BY total_present DESC, total_invites DESC");
    $stmt->execute();
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($rows);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Erreur lors de la récupération des hôtesses : ' . $e->getMessage()]);
}
