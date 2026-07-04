<?php
session_start();
header('Content-Type: application/json');
require_once '../config/db.php';

$notificationConfigPath = __DIR__ . '/../config/notification_config.php';
if (file_exists($notificationConfigPath)) {
    require_once $notificationConfigPath;
}

// Only admin allowed for dashboard marking
if (!isset($_SESSION['user_id'], $_SESSION['nom'], $_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['status' => 'error', 'message' => 'Accès refusé.']);
    exit;
}

$id_invite = $_POST['id_invite'] ?? null;
$statut = $_POST['statut'] ?? 'present';
$validate = isset($_POST['validate']) && $_POST['validate'] === '1';

if (!$id_invite) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Identifiant d\'invité manquant.']);
    exit;
}

$allowed = ['present', 'absent'];
if (!in_array($statut, $allowed, true)) {
    $statut = 'present';
}

try {
    // Update invite status
    $stmt = $conn->prepare("UPDATE invites SET statut = ? WHERE id_invite = ?");
    $stmt->execute([$statut, $id_invite]);

    // Find latest liste_invites entry for this invite
    $listStmt = $conn->prepare("SELECT id_liste_invite, id_even FROM liste_invites WHERE id_invite = ? ORDER BY id_liste_invite DESC LIMIT 1");
    $listStmt->execute([$id_invite]);
    $list = $listStmt->fetch(PDO::FETCH_ASSOC);

    if ($list) {
        $newEstPresent = ($statut === 'present' ? 1 : 0);
        $updateList = $conn->prepare("UPDATE liste_invites SET est_present = ?, enregistrer_par = ?, id_utilisateur = ?, date_validation = NOW() WHERE id_liste_invite = ?");
        $updateList->execute([
            $newEstPresent,
            $_SESSION['nom'],
            $_SESSION['user_id'],
            $list['id_liste_invite']
        ]);
    }

    // Load invite and event info
    $inviteStmt = $conn->prepare("SELECT nom, email, telephone FROM invites WHERE id_invite = ? LIMIT 1");
    $inviteStmt->execute([$id_invite]);
    $invite = $inviteStmt->fetch(PDO::FETCH_ASSOC);

    $event = null;
    if ($list && !empty($list['id_even'])) {
        $evStmt = $conn->prepare("SELECT nom AS title, date_ AS event_date, lieu AS location FROM evenements WHERE id_even = ? LIMIT 1");
        $evStmt->execute([$list['id_even']]);
        $event = $evStmt->fetch(PDO::FETCH_ASSOC);
    }

    $messages = [];
    $eventTitle = $event['title'] ?? 'votre événement';
    $eventDate = $event['event_date'] ?? '';
    $eventLocation = $event['location'] ?? '';

    if ($validate && $statut === 'present' && $invite) {
        $phone = preg_replace('/[^0-9+]/', '', trim($invite['telephone'] ?? ''));
        $name = trim($invite['nom'] ?? '');

        $template = $GLOBALS['WHATSAPP_TEMPLATE'] ?? "Bonjour {nom},\n\nVotre présence à l'événement \"{event_title}\" est confirmée.\nDate: {event_date}\nLieu: {event_location}\n\nMerci de votre participation.\n\nPour tout problème lié à l'application Asso+, veuillez nous contacter au {support_phone}.";
        $body = str_replace(
            ['{nom}', '{event_title}', '{event_date}', '{event_location}', '{support_phone}'],
            [$name, $eventTitle, $eventDate, $eventLocation, $GLOBALS['WHATSAPP_SUPPORT_NUMBER'] ?? '+237 654143860'],
            $template
        );

        $verificationStatus = 'validated';
        $verificationState = 'envoye';
        if (!empty($phone)) {
            $phoneNumber = preg_replace('/^\+/', '', $phone);
            $waBase = $GLOBALS['WHATSAPP_BASE_URL'] ?? 'https://wa.me/';
            $waUrl = rtrim($waBase, '/') . '/' . rawurlencode($phoneNumber) . '?text=' . rawurlencode($body);
            $messages['whatsapp'] = 'ready';
            $messages['whatsapp_url'] = $waUrl;
            $messages['whatsapp_message'] = $body;
        } else {
            $messages['whatsapp'] = 'missing_phone';
            $verificationStatus = 'failed';
            $verificationState = 'erreur';
        }

        if ($list) {
            $verificationStmt = $conn->prepare("INSERT INTO presence_verifications (id_liste_invite, id_invite, id_even, statut, type, contenu, envoyer_par, date_envoi, etat) VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), ?)");
            $verificationStmt->execute([
                $list['id_liste_invite'],
                $id_invite,
                $list['id_even'],
                $verificationStatus,
                'whatsapp',
                $body,
                $_SESSION['nom'],
                $verificationState
            ]);
        }
    }

    echo json_encode(['status' => 'success', 'id_invite' => $id_invite, 'statut' => $statut, 'notify' => $messages]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}

