<?php
require_once '../config/auth.php';
header('Content-Type: application/json');
require_once '../config/db.php';

welcomy_require_auth(['admin', 'hotesse'], $conn);

try {
    $stmt = $conn->query("SELECT e.id_even, e.nom AS title, e.date_ AS event_date, e.lieu AS location, e.description,
        u.nom AS creator,
        COUNT(li.id_liste_invite) AS total_invites
        FROM evenements e
        LEFT JOIN users u ON u.id_utilisateur = e.id_utilisateur
        LEFT JOIN liste_invites li ON li.id_even = e.id_even
        GROUP BY e.id_even, e.nom, e.date_, e.lieu, e.description, u.nom
        ORDER BY e.id_even DESC");
    $events = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($events);
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Erreur lors de la récupération des événements: ' . $e->getMessage()]);
}
