<?php
/**
 * Bootstrap session partagée pour toute l'application Welcomy.
 */
if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();
}

/**
 * Retourne le rôle normalisé de l'utilisateur connecté.
 */
function welcomy_current_role(): string
{
    return strtolower(trim((string)($_SESSION['role'] ?? '')));
}

/**
 * Retourne le nom affiché de l'utilisateur connecté.
 */
function welcomy_current_name(PDO $conn = null): string
{
    if (!empty($_SESSION['nom'])) {
        return (string)$_SESSION['nom'];
    }
    if ($conn && !empty($_SESSION['user_id'])) {
        try {
            $stmt = $conn->prepare('SELECT nom FROM users WHERE id_utilisateur = ? LIMIT 1');
            $stmt->execute([(int)$_SESSION['user_id']]);
            $nom = $stmt->fetchColumn();
            if ($nom) {
                $_SESSION['nom'] = $nom;
                return (string)$nom;
            }
        } catch (PDOException $e) {
            // ignore
        }
    }
    return 'Utilisateur';
}

/**
 * Vérifie l'authentification et le rôle pour les endpoints JSON.
 */
function welcomy_require_auth(array $roles, PDO $conn = null): void
{
    if (empty($_SESSION['user_id'])) {
        http_response_code(403);
        echo json_encode(['status' => 'error', 'message' => 'Accès refusé. Veuillez vous reconnecter.']);
        exit;
    }

    $role = welcomy_current_role();
    if (!in_array($role, $roles, true)) {
        http_response_code(403);
        echo json_encode(['status' => 'error', 'message' => 'Accès refusé.']);
        exit;
    }

    welcomy_current_name($conn);
}

/**
 * URL du tableau de bord selon le rôle.
 */
function welcomy_dashboard_url(?string $role = null): string
{
    $role = $role ?? welcomy_current_role();
    $base = '/welcomy/projet_stage/Frontend';
    if ($role === 'admin') {
        return $base . '/admin/dashboardOrganisateur.php';
    }
    if ($role === 'hotesse') {
        return $base . '/hotesse/dashboardHotesse.php';
    }
    return $base . '/login.php';
}

/**
 * Redirige vers le dashboard si l'utilisateur est déjà connecté.
 */
function welcomy_redirect_if_logged_in(): void
{
    if (empty($_SESSION['user_id'])) {
        return;
    }
    $role = welcomy_current_role();
    if (!in_array($role, ['admin', 'hotesse'], true)) {
        return;
    }
    header('Location: ' . welcomy_dashboard_url($role));
    exit;
}

/**
 * Garantit que la colonne id_utilisateur existe dans liste_invites.
 */
function welcomy_ensure_liste_invites_user_column(PDO $conn): void
{
    static $checked = false;
    if ($checked) {
        return;
    }
    $column = $conn->query("SHOW COLUMNS FROM liste_invites LIKE 'id_utilisateur'")->fetch(PDO::FETCH_ASSOC);
    if (!$column) {
        $conn->exec('ALTER TABLE liste_invites ADD COLUMN id_utilisateur BIGINT NULL AFTER id_liste_invite');
    }
    $checked = true;
}

/**
 * Garantit que contenu accepte les messages WhatsApp complets (TEXT, pas VARCHAR(255)).
 */
function welcomy_ensure_presence_verifications_schema(PDO $conn): void
{
    static $checked = false;
    if ($checked) {
        return;
    }
    welcomy_ensure_liste_invites_user_column($conn);

    $column = $conn->query("SHOW COLUMNS FROM presence_verifications LIKE 'contenu'")->fetch(PDO::FETCH_ASSOC);
    if ($column && stripos($column['Type'], 'varchar') !== false) {
        $conn->exec('ALTER TABLE presence_verifications MODIFY COLUMN contenu TEXT COLLATE utf8mb4_general_ci DEFAULT NULL');
    }
    $checked = true;
}
