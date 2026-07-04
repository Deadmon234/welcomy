<?php
require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../helpers/phone_helper.php';
header('Content-Type: application/json');
require_once __DIR__ . '/../config/db.php';

welcomy_require_auth(['hotesse', 'admin'], $conn);

if (!isset($_POST['nom'], $_POST['telephone'], $_POST['event_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Données manquantes.']);
    exit;
}

$eventId = (int)$_POST['event_id'];
if ($eventId <= 0) {
    echo json_encode(['status' => 'error', 'message' => 'Événement invalide.']);
    exit;
}

$nom = trim($_POST['nom']);
$dialCode = trim($_POST['country_dial'] ?? '+237');
$telephone = welcomy_normalize_phone(trim($_POST['telephone']), $dialCode);

if ($nom === '') {
    echo json_encode(['status' => 'error', 'message' => 'Le nom est requis.']);
    exit;
}

if (!$telephone) {
    echo json_encode(['status' => 'error', 'message' => 'Numéro de téléphone invalide pour le pays sélectionné.']);
    exit;
}

try {
    welcomy_ensure_liste_invites_user_column($conn);

    $stmt = $conn->prepare("INSERT INTO invites (nom, email, telephone, statut) VALUES (?, ?, ?, 'absent')");
    $stmt->execute([
        $nom,
        trim($_POST['email'] ?? ''),
        $telephone,
    ]);
    $inviteId = $conn->lastInsertId();

    $senderName = welcomy_current_name($conn);
    $stmt = $conn->prepare("INSERT INTO liste_invites (id_invite, id_even, est_present, enregistrer_par, id_utilisateur, date_validation) VALUES (?, ?, 0, ?, ?, NOW())");
    $stmt->execute([
        $inviteId,
        $eventId,
        $senderName,
        (int)$_SESSION['user_id'],
    ]);

    echo json_encode([
        'status' => 'success',
        'message' => 'Invité ajouté avec succès.',
        'invite_id' => $inviteId,
        'event_id' => $eventId,
        'telephone' => $telephone,
    ]);
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Erreur lors de l\'insertion : ' . $e->getMessage()]);
}
