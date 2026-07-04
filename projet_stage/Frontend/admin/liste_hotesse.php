<?php
session_start();
require_once '../../../Backend/config/db.php';

if (!isset($_SESSION['user_id'], $_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

$hostesses = [];
$errorMessage = '';

try {
    $column = $conn->query("SHOW COLUMNS FROM liste_invites LIKE 'id_utilisateur'")->fetch(PDO::FETCH_ASSOC);
    if (!$column) {
        $conn->exec("ALTER TABLE liste_invites ADD COLUMN id_utilisateur BIGINT NULL AFTER id_liste_invite");
    }

    $stmt = $conn->prepare("SELECT u.id_utilisateur, u.nom, u.email,
        COUNT(li.id_liste_invite) AS total_invites,
        SUM(CASE WHEN li.est_present = 1 THEN 1 ELSE 0 END) AS total_present
        FROM users u
        LEFT JOIN liste_invites li ON li.id_utilisateur = u.id_utilisateur
        WHERE u.role = 'hotesse'
        GROUP BY u.id_utilisateur, u.nom, u.email
        ORDER BY total_present DESC, total_invites DESC");
    $stmt->execute();
    $hostesses = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $errorMessage = $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>WELCOMY — Liste des hôtesses</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <style>body{font-family:'Inter',system-ui,sans-serif}.glass{background:rgba(30,41,59,.7);backdrop-filter:blur(12px)}</style>
</head>
<body class="bg-slate-950 text-slate-100 min-h-screen">

  <header class="sticky top-0 z-40 border-b border-slate-800/80 glass">
    <div class="max-w-6xl mx-auto px-4 py-4 flex items-center justify-between gap-4">
      <div class="flex items-center gap-3">
        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-600 to-indigo-800 flex items-center justify-center">
          <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
        </div>
        <div>
          <h1 class="text-lg font-bold text-white">Liste des hôtesses</h1>
          <p class="text-xs text-slate-400">Suivi des invités marqués présents par chaque hôtesse</p>
        </div>
      </div>
      <a href="dashboardOrganisateur.php" class="inline-flex items-center gap-2 bg-slate-800 hover:bg-slate-700 border border-slate-700 px-4 py-2.5 rounded-xl text-sm font-medium text-slate-300">← Retour au dashboard</a>
    </div>
  </header>

  <main class="max-w-6xl mx-auto px-4 py-6">
    <?php if ($errorMessage): ?>
      <div class="bg-red-500/10 border border-red-500/30 rounded-2xl p-4 text-red-400 text-sm mb-6">Erreur : <?= htmlspecialchars($errorMessage, ENT_QUOTES, 'UTF-8') ?></div>
    <?php endif; ?>

    <div class="space-y-3">
      <?php if (empty($hostesses) && !$errorMessage): ?>
        <div class="text-center py-16 text-slate-400 text-sm">Aucune hôtesse trouvée.</div>
      <?php else: ?>
        <?php foreach ($hostesses as $h): ?>
          <?php
            $total = (int)($h['total_invites'] ?? 0);
            $present = (int)($h['total_present'] ?? 0);
            $rate = $total > 0 ? round(($present / $total) * 100) : 0;
          ?>
          <div class="bg-slate-800/60 border border-slate-700/50 rounded-2xl p-5 hover:border-slate-600 transition-all">
            <div class="flex flex-col sm:flex-row sm:items-center gap-4">
              <div class="flex items-center gap-3 flex-1 min-w-0">
                <div class="w-11 h-11 rounded-full bg-violet-500/20 text-violet-400 flex items-center justify-center text-sm font-bold shrink-0">
                  <?= strtoupper(mb_substr($h['nom'], 0, 1)) ?>
                </div>
                <div class="min-w-0">
                  <p class="font-semibold text-white truncate"><?= htmlspecialchars($h['nom'], ENT_QUOTES, 'UTF-8') ?></p>
                  <p class="text-sm text-slate-400 truncate"><?= htmlspecialchars($h['email'], ENT_QUOTES, 'UTF-8') ?></p>
                </div>
              </div>
              <div class="flex items-center gap-6 sm:gap-8">
                <div class="text-center">
                  <p class="text-xl font-bold text-white"><?= $total ?></p>
                  <p class="text-xs text-slate-500">Invités créés</p>
                </div>
                <div class="text-center">
                  <p class="text-xl font-bold text-emerald-400"><?= $present ?></p>
                  <p class="text-xs text-slate-500">Présents</p>
                </div>
                <div class="text-center min-w-[60px]">
                  <p class="text-xl font-bold text-violet-400"><?= $rate ?>%</p>
                  <p class="text-xs text-slate-500">Taux</p>
                </div>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </main>
</body>
</html>
