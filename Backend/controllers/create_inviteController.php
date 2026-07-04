<?php
session_start();
// Affichage des erreurs (en dev seulement)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Connexion à la base
require_once '../config/db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'], $_SESSION['role']) || !in_array($_SESSION['role'], ['hotesse', 'admin'], true)) {
    http_response_code(403);
    echo json_encode(['status' => 'error', 'message' => 'Accès refusé.']);
    exit;
}

if (!isset($_POST['nom'], $_POST['telephone'], $_POST['event_id'])) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Données manquantes.'
    ]);
    exit;
}

$eventId = (int) $_POST['event_id'];
if ($eventId <= 0) {
    echo json_encode(['status' => 'error', 'message' => 'Événement invalide.']);
    exit;
}

try {
    $column = $conn->query("SHOW COLUMNS FROM liste_invites LIKE 'id_utilisateur'")->fetch();
    if (!$column) {
        $conn->exec("ALTER TABLE liste_invites ADD COLUMN id_utilisateur BIGINT NULL AFTER id_liste_invite");
    }

    $stmt = $conn->prepare("INSERT INTO invites (nom, email, telephone, statut) VALUES (?, ?, ?, 'absent')");
    $stmt->execute([
        $_POST['nom'],
        $_POST['email'] ?? '',
        $_POST['telephone']
    ]);
    $inviteId = $conn->lastInsertId();

    $hostName = $_SESSION['nom'];
    $stmt = $conn->prepare("INSERT INTO liste_invites (id_invite, id_even, est_present, enregistrer_par, id_utilisateur, date_validation) VALUES (?, ?, 0, ?, ?, NOW())");
    $stmt->execute([
        $inviteId,
        $eventId,
        $hostName,
        $_SESSION['user_id']
    ]);

    echo json_encode([
        'status' => 'success',
        'message' => 'Invité ajouté avec succès.',
        'invite_id' => $inviteId,
        'event_id' => $eventId
    ]);
} catch (PDOException $e) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Erreur lors de l\'insertion : ' . $e->getMessage()
    ]);
}
