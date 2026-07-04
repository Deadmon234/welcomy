<?php
session_start();
header('Content-Type: application/json');
require_once '../config/db.php';

if (!isset($_SESSION['user_id'], $_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['status' => 'error', 'message' => 'Accès refusé.']);
    exit;
}

$event_id = isset($_GET['event_id']) ? (int)$_GET['event_id'] : null;

try {
    if ($event_id) {
        $stmt = $conn->prepare("SELECT i.id_invite, i.nom, i.email, i.telephone, i.statut,
            li.id_liste_invite, li.est_present, li.enregistrer_par, li.date_validation,
            e.nom AS event_title, e.date_ AS event_date, e.lieu AS event_location,
            COALESCE(pv.remerciement_envoye, 0) AS remerciement_envoye,
            pv.remerciement_par, pv.date_remerciement
            FROM liste_invites li
            JOIN invites i ON i.id_invite = li.id_invite
            LEFT JOIN evenements e ON e.id_even = li.id_even
            LEFT JOIN presence_verifications pv ON pv.id_verification = (
                SELECT MAX(pv2.id_verification)
                FROM presence_verifications pv2
                WHERE pv2.id_liste_invite = li.id_liste_invite
            )
            WHERE li.id_even = ?
            ORDER BY li.date_validation DESC");
        $stmt->execute([$event_id]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($rows);
        exit;
    }

    $stmt = $conn->query("SELECT e.id_even, e.nom AS event_title, e.date_ AS event_date, e.lieu AS event_location,
        COUNT(li.id_liste_invite) AS total_invites,
        SUM(CASE WHEN li.est_present = 1 THEN 1 ELSE 0 END) AS total_present,
        SUM(CASE WHEN li.est_present = 0 THEN 1 ELSE 0 END) AS total_absent
        FROM evenements e
        LEFT JOIN liste_invites li ON li.id_even = e.id_even
        GROUP BY e.id_even, e.nom, e.date_, e.lieu
        ORDER BY e.date_ DESC");
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($rows);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Erreur lors de la récupération : ' . $e->getMessage()]);
}
