<?php
require_once __DIR__ . '/../config/auth.php';
header('Content-Type: application/json');
require_once __DIR__ . '/../config/db.php';

$notificationConfigPath = __DIR__ . '/../config/notification_config.php';
if (file_exists($notificationConfigPath)) {
    require_once $notificationConfigPath;
}

welcomy_require_auth(['admin'], $conn);

$id_invite = $_POST['id_invite'] ?? null;
$event_id = isset($_POST['event_id']) ? (int)$_POST['event_id'] : null;
if (!$id_invite || !$event_id) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Identifiant d\'invité ou d\'événement manquant.']);
    exit;
}

try {
    $listStmt = $conn->prepare("SELECT li.id_liste_invite, li.id_even, li.est_present
        FROM liste_invites li
        WHERE li.id_invite = ? AND li.id_even = ?
        LIMIT 1");
    $listStmt->execute([$id_invite, $event_id]);
    $list = $listStmt->fetch(PDO::FETCH_ASSOC);

    if (!$list) {
        http_response_code(404);
        echo json_encode(['status' => 'error', 'message' => 'Invité introuvable pour cet événement.']);
        exit;
    }

    if ((int)$list['est_present'] !== 1) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Seuls les invités présents peuvent être remerciés.']);
        exit;
    }

    $verifyStmt = $conn->prepare("SELECT id_verification, remerciement_envoye
        FROM presence_verifications
        WHERE id_liste_invite = ?
        ORDER BY id_verification DESC
        LIMIT 1");
    $verifyStmt->execute([$list['id_liste_invite']]);
    $verification = $verifyStmt->fetch(PDO::FETCH_ASSOC);

    if ($verification && (int)$verification['remerciement_envoye'] === 1) {
        http_response_code(409);
        echo json_encode(['status' => 'error', 'message' => 'Le remerciement a déjà été envoyé pour cet invité.']);
        exit;
    }

    $inviteStmt = $conn->prepare("SELECT nom, telephone FROM invites WHERE id_invite = ? LIMIT 1");
    $inviteStmt->execute([$id_invite]);
    $invite = $inviteStmt->fetch(PDO::FETCH_ASSOC);

    if (!$invite) {
        http_response_code(404);
        echo json_encode(['status' => 'error', 'message' => 'Invité introuvable.']);
        exit;
    }

    $evStmt = $conn->prepare("SELECT nom AS title, date_ AS event_date, lieu AS location FROM evenements WHERE id_even = ? LIMIT 1");
    $evStmt->execute([$list['id_even']]);
    $event = $evStmt->fetch(PDO::FETCH_ASSOC);

    $eventTitle = $event['title'] ?? 'votre événement';
    $eventDate = $event['event_date'] ?? '';
    $eventLocation = $event['location'] ?? '';
    $name = trim($invite['nom'] ?? '');
    $phone = preg_replace('/[^0-9+]/', '', trim($invite['telephone'] ?? ''));

    $template = $GLOBALS['WHATSAPP_THANKYOU_TEMPLATE'] ?? "Bonjour {nom},\n\nMerci pour votre participation à l'événement « {event_title} ».\n\nService client Asso+ : {support_phone}.";
    $body = str_replace(
        ['{nom}', '{event_title}', '{event_date}', '{event_location}', '{support_phone}'],
        [$name, $eventTitle, $eventDate, $eventLocation, $GLOBALS['WHATSAPP_SUPPORT_NUMBER'] ?? '+237 654143860'],
        $template
    );

    $sender = welcomy_current_name($conn);
    $notify = [];

    if ($verification) {
        $updateStmt = $conn->prepare("UPDATE presence_verifications
            SET remerciement_envoye = 1,
                remerciement_par = ?,
                contenu_remerciement = ?,
                date_remerciement = NOW()
            WHERE id_verification = ?");
        $updateStmt->execute([$sender, $body, $verification['id_verification']]);
    } else {
        $insertStmt = $conn->prepare("INSERT INTO presence_verifications
            (id_liste_invite, id_invite, id_even, statut, type, contenu, envoyer_par, date_envoi, etat,
             remerciement_envoye, remerciement_par, contenu_remerciement, date_remerciement)
            VALUES (?, ?, ?, 'validated', 'whatsapp', NULL, ?, NOW(), 'envoye', 1, ?, ?, NOW())");
        $insertStmt->execute([
            $list['id_liste_invite'],
            $id_invite,
            $list['id_even'],
            $sender,
            $sender,
            $body
        ]);
    }

    if (!empty($phone)) {
        $phoneNumber = preg_replace('/^\+/', '', $phone);
        $waBase = $GLOBALS['WHATSAPP_BASE_URL'] ?? 'https://wa.me/';
        $notify['whatsapp_url'] = rtrim($waBase, '/') . '/' . rawurlencode($phoneNumber) . '?text=' . rawurlencode($body);
        $notify['whatsapp'] = 'ready';
    } else {
        $notify['whatsapp'] = 'missing_phone';
    }

    echo json_encode([
        'status' => 'success',
        'id_invite' => $id_invite,
        'remerciement_envoye' => 1,
        'remerciement_par' => $sender,
        'notify' => $notify
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
