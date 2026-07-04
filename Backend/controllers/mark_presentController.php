<?php
require_once __DIR__ . '/../config/auth.php';
header('Content-Type: application/json');
require_once __DIR__ . '/../config/db.php';

$notificationConfigPath = __DIR__ . '/../config/notification_config.php';
if (file_exists($notificationConfigPath)) {
    require_once $notificationConfigPath;
}

welcomy_require_auth(['admin', 'hotesse'], $conn);

$id_invite = $_POST['id_invite'] ?? null;
$event_id = isset($_POST['event_id']) ? (int)$_POST['event_id'] : null;
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

$senderName = welcomy_current_name($conn);
$userId = (int)$_SESSION['user_id'];

try {
    welcomy_ensure_presence_verifications_schema($conn);

    $stmt = $conn->prepare('UPDATE invites SET statut = ? WHERE id_invite = ?');
    $stmt->execute([$statut, $id_invite]);

    if ($event_id) {
        $listStmt = $conn->prepare('SELECT id_liste_invite, id_even FROM liste_invites WHERE id_invite = ? AND id_even = ? LIMIT 1');
        $listStmt->execute([$id_invite, $event_id]);
    } else {
        $listStmt = $conn->prepare('SELECT id_liste_invite, id_even FROM liste_invites WHERE id_invite = ? ORDER BY id_liste_invite DESC LIMIT 1');
        $listStmt->execute([$id_invite]);
    }
    $list = $listStmt->fetch(PDO::FETCH_ASSOC);

    if ($list) {
        $newEstPresent = ($statut === 'present' ? 1 : 0);
        $updateList = $conn->prepare('UPDATE liste_invites SET est_present = ?, enregistrer_par = ?, id_utilisateur = ?, date_validation = NOW() WHERE id_liste_invite = ?');
        $updateList->execute([
            $newEstPresent,
            $senderName,
            $userId,
            $list['id_liste_invite'],
        ]);
    }

    $inviteStmt = $conn->prepare('SELECT nom, email, telephone FROM invites WHERE id_invite = ? LIMIT 1');
    $inviteStmt->execute([$id_invite]);
    $invite = $inviteStmt->fetch(PDO::FETCH_ASSOC);

    $event = null;
    if ($list && !empty($list['id_even'])) {
        $evStmt = $conn->prepare('SELECT nom AS title, date_ AS event_date, lieu AS location FROM evenements WHERE id_even = ? LIMIT 1');
        $evStmt->execute([$list['id_even']]);
        $event = $evStmt->fetch(PDO::FETCH_ASSOC);
    }

    $messages = [];
    $eventTitle = $event['title'] ?? 'votre événement';
    $eventDate = $event['event_date'] ?? '';
    $eventLocation = $event['location'] ?? '';

    // Confirmation de présence (distincte du remerciement post-événement)
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
            $verificationStmt = $conn->prepare("INSERT INTO presence_verifications
                (id_liste_invite, id_invite, id_even, statut, type, contenu, envoyer_par, date_envoi, etat, remerciement_envoye)
                VALUES (?, ?, ?, ?, 'whatsapp', ?, ?, NOW(), ?, 0)");
            $verificationStmt->execute([
                $list['id_liste_invite'],
                $id_invite,
                $list['id_even'],
                $verificationStatus,
                $body,
                $senderName,
                $verificationState,
            ]);
        }
    }

    echo json_encode(['status' => 'success', 'id_invite' => $id_invite, 'statut' => $statut, 'notify' => $messages]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
