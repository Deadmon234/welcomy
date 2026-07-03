<?php
header('Content-Type: application/json');
require_once '../config/db.php';

try {
    $counts = [];
    foreach (['evenements', 'invites', 'liste_invites'] as $table) {
        $stmt = $conn->query("SELECT COUNT(*) AS c FROM $table");
        $counts[$table] = (int)$stmt->fetch(PDO::FETCH_ASSOC)['c'];
    }
    $sample = [];
    $stmt = $conn->query("SELECT * FROM liste_invites LIMIT 10");
    $sample['liste_invites'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode(['status' => 'success', 'counts' => $counts, 'sample' => $sample]);
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
