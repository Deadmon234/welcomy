<?php
header('Content-Type: application/json');
require_once '../config/db.php';

try {
    $stmt = $conn->query("SELECT e.id_even, e.nom AS title, e.date_ AS event_date, e.lieu AS location, e.description, u.nom AS creator
        FROM evenements e
        LEFT JOIN users u ON u.id_utilisateur = e.id_utilisateur
        ORDER BY e.id_even DESC");
    $events = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($events);
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Erreur lors de la récupération des événements: ' . $e->getMessage()]);
}
