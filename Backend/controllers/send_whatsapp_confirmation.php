<?php
session_start();
header('Content-Type: application/json');
require_once '../config/db.php';

if (!isset($_SESSION['user_id'], $_SESSION['role']) || !in_array($_SESSION['role'], ['admin', 'hotesse'], true)) {
    http_response_code(403);
    echo json_encode(['message' => 'Accès refusé.']);
    exit;
}

$notificationConfigPath = __DIR__ . '/../config/notification_config.php';
if (file_exists($notificationConfigPath)) {
    require_once $notificationConfigPath;
}

$id_invite = $_POST['id_invite'] ?? null;
if (!$id_invite) {
    http_response_code(400);
    echo json_encode(['message' => 'Identifiant d\'invité manquant.']);
    exit;
}

try {
    $inviteStmt = $conn->prepare("SELECT i.nom, i.email, i.telephone, e.nom AS event_title, e.date_ AS event_date, e.lieu AS event_location
        FROM invites i
        JOIN liste_invites li ON li.id_invite = i.id_invite
        LEFT JOIN evenements e ON e.id_even = li.id_even
        WHERE i.id_invite = ?");
    $inviteStmt->execute([$id_invite]);
    $invite = $inviteStmt->fetch(PDO::FETCH_ASSOC);

    if (!$invite) {
        http_response_code(404);
        echo json_encode(['message' => 'Invité introuvable.']);
        exit;
    }

    $phone = preg_replace('/[^0-9+]/', '', trim($invite['telephone'] ?? ''));
    if (empty($phone)) {
        echo json_encode(['message' => 'Numéro de téléphone manquant pour l\'invité.']);
        exit;
    }

    $template = $GLOBALS['WHATSAPP_TEMPLATE'] ?? "Bonjour {nom},\n\nVotre présence à l'événement \"{event_title}\" a été confirmée.\nDate: {event_date}\nLieu: {event_location}\n\nMerci de votre participation.\n\nPour tout problème lié à l'application Asso+, veuillez nous contacter au {support_phone}.";
    $body = str_replace(
        ['{nom}', '{event_title}', '{event_date}', '{event_location}', '{support_phone}'],
        [trim($invite['nom'] ?? ''), trim($invite['event_title'] ?? ''), trim($invite['event_date'] ?? ''), trim($invite['event_location'] ?? ''), $GLOBALS['WHATSAPP_SUPPORT_NUMBER'] ?? '+237 654143860'],
        $template
    );

    $phoneNumber = preg_replace('/^\+/', '', $phone);
    $waBase = $GLOBALS['WHATSAPP_BASE_URL'] ?? 'https://wa.me/';
    $waUrl = rtrim($waBase, '/') . '/' . rawurlencode($phoneNumber) . '?text=' . rawurlencode($body);

    echo json_encode(['whatsapp_url' => $waUrl, 'message' => $body]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['message' => 'Erreur serveur : ' . $e->getMessage()]);
}
