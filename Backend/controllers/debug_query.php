<?php
header('Content-Type: application/json');
require_once '../config/db.php';

try {
    $invites = $conn->query('SELECT * FROM invites ORDER BY id_invite ASC')->fetchAll(PDO::FETCH_ASSOC);
    $liste = $conn->query('SELECT * FROM liste_invites ORDER BY id_liste_invite ASC')->fetchAll(PDO::FETCH_ASSOC);
    $join = $conn->prepare('SELECT li.*, i.nom AS invite_nom, i.telephone AS invite_telephone, i.statut AS invite_statut FROM liste_invites li LEFT JOIN invites i ON i.id_invite = li.id_invite WHERE li.id_even = ?');
    $join->execute([3]);
    $joinRows = $join->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode(['status' => 'success', 'invites' => $invites, 'liste' => $liste, 'join_event_3' => $joinRows]);
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
