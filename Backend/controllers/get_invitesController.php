<?php
header('Content-Type: application/json');
require_once '../config/db.php';

$event_id = isset($_GET['event_id']) ? (int)$_GET['event_id'] : null;

try {
    if ($event_id) {
        $stmt = $conn->prepare("SELECT li.id_liste_invite, COALESCE(i.id_invite, li.id_invite) AS id_invite, COALESCE(i.nom, 'Invité supprimé') AS nom, COALESCE(i.email, '') AS email, COALESCE(i.telephone, '') AS telephone, COALESCE(i.statut, 'absent') AS statut, li.est_present, li.enregistrer_par, li.id_even, li.id_utilisateur,
            e.nom AS event_title, e.date_ AS event_date, e.lieu AS event_location
            FROM liste_invites li
            LEFT JOIN invites i ON i.id_invite = li.id_invite
            LEFT JOIN evenements e ON e.id_even = li.id_even
            WHERE li.id_even = ?
            ORDER BY li.id_liste_invite DESC");
        $stmt->execute([$event_id]);
    } else {
        $stmt = $conn->query("SELECT li.id_liste_invite, COALESCE(i.id_invite, li.id_invite) AS id_invite, COALESCE(i.nom, 'Invité supprimé') AS nom, COALESCE(i.email, '') AS email, COALESCE(i.telephone, '') AS telephone, COALESCE(i.statut, 'absent') AS statut, li.est_present, li.enregistrer_par, li.id_even, li.id_utilisateur,
            e.nom AS event_title, e.date_ AS event_date, e.lieu AS event_location
            FROM liste_invites li
            LEFT JOIN invites i ON i.id_invite = li.id_invite
            LEFT JOIN evenements e ON e.id_even = li.id_even
            ORDER BY li.id_liste_invite DESC");
    }

    $invites = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($invites);
} catch (PDOException $e) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Erreur lors de la récupération des invités : ' . $e->getMessage()
    ]);
}
