<?php
require_once __DIR__ . '/../../../Backend/config/auth.php';
if (!isset($_SESSION['user_id'], $_SESSION['role']) || welcomy_current_role() !== 'admin') {
    header('Location: ../login.php');
    exit;
}
require_once __DIR__ . '/../../../Backend/config/notification_config.php';
$adminName = htmlspecialchars($_SESSION['nom'], ENT_QUOTES, 'UTF-8');
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>WELCOMY — Espace Organisateur</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <script>
    tailwind.config = { theme: { extend: { fontFamily: { sans: ['Inter', 'system-ui', 'sans-serif'] }, animation: { 'fade-in': 'fadeIn .3s ease-out' }, keyframes: { fadeIn: { from: { opacity: 0, transform: 'translateY(8px)' }, to: { opacity: 1, transform: 'translateY(0)' } } } } } };
  </script>
  <style>body{font-family:'Inter',system-ui,sans-serif}.glass{background:rgba(30,41,59,.7);backdrop-filter:blur(12px)}</style>
</head>
<body class="bg-slate-950 text-slate-100 min-h-screen">

  <header class="sticky top-0 z-40 border-b border-slate-800/80 glass">
    <div class="max-w-6xl mx-auto px-4 py-4 flex flex-col lg:flex-row items-start lg:items-center justify-between gap-4">
      <div class="flex items-center gap-3">
        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-violet-600 to-indigo-800 flex items-center justify-center shadow-lg shadow-violet-900/30">
          <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
        </div>
        <div>
          <h1 class="text-lg font-bold text-white tracking-tight">WELCOMY</h1>
          <p class="text-xs text-slate-400">Espace organisateur · Bonjour, <?= $adminName ?></p>
        </div>
      </div>
      <div class="flex flex-wrap items-center gap-2 w-full lg:w-auto">
        <button id="openInviteModal" class="inline-flex items-center gap-2 bg-violet-600 hover:bg-violet-500 px-4 py-2.5 rounded-xl text-sm font-semibold transition-colors">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
          Ajouter invité
        </button>
        <button id="openCreateEvent" class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-500 px-4 py-2.5 rounded-xl text-sm font-semibold transition-colors">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
          Créer événement
        </button>
        <a href="liste_hotesse.php" class="inline-flex items-center gap-2 bg-slate-800 hover:bg-slate-700 border border-slate-700 px-4 py-2.5 rounded-xl text-sm font-medium text-slate-300 transition-colors">Hôtesses</a>
        <a href="gestion.php" class="inline-flex items-center gap-2 bg-slate-800 hover:bg-slate-700 border border-slate-700 px-4 py-2.5 rounded-xl text-sm font-medium text-slate-300 transition-colors">Gestion</a>
        <a href="verification_evenement.php" class="inline-flex items-center gap-2 bg-slate-800 hover:bg-slate-700 border border-slate-700 px-4 py-2.5 rounded-xl text-sm font-medium text-slate-300 transition-colors">Vérifier présences</a>
        <form action="../logout.php" method="POST">
          <button type="submit" class="inline-flex items-center gap-2 bg-slate-800 hover:bg-slate-700 border border-slate-700 px-4 py-2.5 rounded-xl text-sm font-medium text-slate-300 transition-colors">Déconnexion</button>
        </form>
      </div>
    </div>
  </header>

  <main class="max-w-6xl mx-auto px-4 py-6 space-y-6">
    <section class="bg-slate-900/50 border border-slate-800 rounded-2xl p-5">
      <label for="eventSelect" class="block text-sm font-medium text-slate-400 mb-2">Événement actif</label>
      <select id="eventSelect" class="w-full bg-slate-800 border border-slate-700 text-white rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-violet-500">
        <option value="" disabled selected>— Choisir un événement —</option>
      </select>
      <div id="eventInfoCard" class="hidden mt-4 pt-4 border-t border-slate-800">
        <h2 id="eventTitle" class="text-lg font-semibold text-white"></h2>
        <div id="eventMeta" class="mt-2 flex flex-col sm:flex-row gap-3 text-sm text-slate-400"></div>
      </div>
    </section>

    <section class="grid grid-cols-3 gap-3 sm:gap-4">
      <div class="bg-slate-900/50 border border-slate-800 rounded-2xl p-4 text-center">
        <p id="statTotal" class="text-2xl sm:text-3xl font-bold text-white">0</p>
        <p class="text-xs sm:text-sm text-slate-400 mt-1">Total invités</p>
      </div>
      <div class="bg-slate-900/50 border border-emerald-900/40 rounded-2xl p-4 text-center">
        <p id="statPresent" class="text-2xl sm:text-3xl font-bold text-emerald-400">0</p>
        <p class="text-xs sm:text-sm text-slate-400 mt-1">Présents</p>
      </div>
      <div class="bg-slate-900/50 border border-slate-800 rounded-2xl p-4 text-center">
        <p id="statAbsent" class="text-2xl sm:text-3xl font-bold text-slate-400">0</p>
        <p class="text-xs sm:text-sm text-slate-400 mt-1">En attente</p>
      </div>
    </section>

    <section class="flex flex-col sm:flex-row gap-3">
      <div class="relative flex-1">
        <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
        <input id="search" type="text" placeholder="Rechercher un invité…" class="w-full bg-slate-900/50 border border-slate-800 rounded-xl pl-10 pr-4 py-3 text-sm text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-violet-500" />
      </div>
      <div class="flex gap-2">
        <button data-filter="all" class="px-4 py-3 rounded-xl text-sm font-medium bg-violet-600 text-white">Tous</button>
        <button data-filter="present" class="filter-btn px-4 py-3 rounded-xl text-sm font-medium bg-slate-800 text-slate-400">Présents</button>
        <button data-filter="absent" class="filter-btn px-4 py-3 rounded-xl text-sm font-medium bg-slate-800 text-slate-400">En attente</button>
      </div>
    </section>

    <section>
      <div id="guestList" class="space-y-3"></div>
      <div id="emptyState" class="text-center py-16">
        <div class="w-16 h-16 mx-auto mb-4 rounded-2xl bg-slate-800 flex items-center justify-center">
          <svg class="w-8 h-8 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
        </div>
        <p class="text-slate-400 text-sm">Sélectionnez un événement pour afficher la liste des invités.</p>
      </div>
    </section>

    <section class="flex flex-col sm:flex-row gap-3">
      <button id="exportExcel" class="inline-flex items-center justify-center gap-2 bg-slate-800 hover:bg-slate-700 border border-slate-700 px-5 py-3 rounded-xl text-sm font-semibold text-slate-300 transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
        Exporter Excel
      </button>
      <button id="refreshList" class="inline-flex items-center justify-center gap-2 bg-slate-800 hover:bg-slate-700 border border-slate-700 px-5 py-3 rounded-xl text-sm font-semibold text-slate-300 transition-colors">Actualiser</button>
    </section>
  </main>

  <!-- WhatsApp modal -->
  <div id="whatsappModal" class="fixed inset-0 hidden z-50 flex items-end sm:items-center justify-center bg-black/60 backdrop-blur-sm p-4">
    <div class="w-full max-w-lg bg-slate-900 border border-slate-700 rounded-2xl shadow-2xl animate-fade-in">
      <div class="p-6 border-b border-slate-800">
        <h3 class="text-lg font-semibold text-white">Confirmer la présence</h3>
        <p class="text-sm text-slate-400 mt-1">Un message WhatsApp sera préparé pour l'invité</p>
      </div>
      <div class="p-6 space-y-4">
        <div class="bg-slate-800/50 rounded-xl p-3">
          <p id="whatsappGuestName" class="font-semibold text-white"></p>
          <p id="whatsappGuestPhone" class="text-sm text-slate-400"></p>
        </div>
        <div>
          <p class="text-xs font-medium text-slate-500 uppercase tracking-wider mb-2">Aperçu du message</p>
          <div id="whatsappPreview" class="bg-[#075e54]/10 border border-emerald-900/30 rounded-xl p-4 text-sm text-slate-300 whitespace-pre-wrap max-h-48 overflow-y-auto"></div>
        </div>
        <div id="whatsappPhoneWarning" class="hidden bg-amber-500/10 border border-amber-500/30 rounded-xl p-3 text-sm text-amber-400">⚠ Aucun numéro renseigné. La présence sera enregistrée sans WhatsApp.</div>
      </div>
      <div class="p-6 pt-0 flex flex-col sm:flex-row gap-3">
        <button id="whatsappCancelBtn" class="flex-1 px-4 py-3 rounded-xl text-sm font-medium bg-slate-800 hover:bg-slate-700 text-slate-300">Annuler</button>
        <button id="whatsappConfirmBtn" class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-3 rounded-xl text-sm font-semibold bg-emerald-600 hover:bg-emerald-500 text-white">Marquer présent et ouvrir WhatsApp</button>
      </div>
    </div>
  </div>

  <!-- Create event modal -->
  <div id="createEventModal" class="fixed inset-0 hidden z-50 flex items-end sm:items-center justify-center bg-black/60 backdrop-blur-sm p-4">
    <div class="w-full max-w-lg bg-slate-900 border border-slate-700 rounded-2xl shadow-2xl animate-fade-in">
      <div class="p-6 border-b border-slate-800 flex items-center justify-between">
        <div><h3 class="text-lg font-semibold text-white">Créer un événement</h3><p class="text-sm text-slate-400">Renseignez les informations de l'événement</p></div>
        <button id="cancelCreateEvent" class="text-slate-500 hover:text-white p-1"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
      </div>
      <div class="p-6 space-y-4">
        <input id="eventTitle" placeholder="Titre de l'événement" class="w-full bg-slate-800 border border-slate-700 rounded-xl px-4 py-3 text-sm text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-violet-500" />
        <input id="eventDate" type="datetime-local" class="w-full bg-slate-800 border border-slate-700 rounded-xl px-4 py-3 text-sm text-white focus:outline-none focus:ring-2 focus:ring-violet-500" />
        <input id="eventLocation" placeholder="Lieu" class="w-full bg-slate-800 border border-slate-700 rounded-xl px-4 py-3 text-sm text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-violet-500" />
        <textarea id="eventDescription" placeholder="Description (optionnel)" rows="3" class="w-full bg-slate-800 border border-slate-700 rounded-xl px-4 py-3 text-sm text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-violet-500"></textarea>
        <button id="createEventBtn" class="w-full bg-violet-600 hover:bg-violet-500 text-white font-semibold py-3 rounded-xl transition-colors">Créer l'événement</button>
      </div>
    </div>
  </div>

  <!-- Add invite modal -->
  <div id="inviteModal" class="fixed inset-0 hidden z-50 flex items-end sm:items-center justify-center bg-black/60 backdrop-blur-sm p-4">
    <div class="w-full max-w-md bg-slate-900 border border-slate-700 rounded-2xl shadow-2xl animate-fade-in">
      <div class="p-6 border-b border-slate-800 flex items-center justify-between">
        <div><h3 class="text-lg font-semibold text-white">Nouvel invité</h3><p class="text-sm text-slate-400">Inscrire un participant</p></div>
        <button id="cancelInviteModal" class="text-slate-500 hover:text-white p-1"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
      </div>
      <div class="p-6 space-y-4">
        <input id="inviteName" placeholder="Nom complet" class="w-full bg-slate-800 border border-slate-700 rounded-xl px-4 py-3 text-sm text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-violet-500" />
        <div id="invitePhoneField" data-phone-field data-default-country="+237">
          <label class="block text-xs font-medium text-slate-400 mb-1.5">Téléphone (WhatsApp) *</label>
          <div class="flex gap-2">
            <select data-phone-country class="w-[11.5rem] shrink-0 bg-slate-800 border border-slate-700 rounded-xl px-2 py-3 text-sm text-white focus:outline-none focus:ring-2 focus:ring-violet-500"></select>
            <input data-phone-number type="tel" inputmode="tel" autocomplete="tel-national" class="flex-1 min-w-0 bg-slate-800 border border-slate-700 rounded-xl px-4 py-3 text-sm text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-violet-500" />
          </div>
          <input type="hidden" data-phone-full id="inviteTelephone" />
          <input type="hidden" data-phone-dial-sync name="country_dial" />
          <p data-phone-hint class="text-xs text-slate-500 mt-1.5"></p>
        </div>
        <select id="inviteEventSelect" class="w-full bg-slate-800 border border-slate-700 rounded-xl px-4 py-3 text-sm text-white focus:outline-none focus:ring-2 focus:ring-violet-500"><option value="">— Choisir un événement —</option></select>
        <div id="inviteMessage" class="text-sm"></div>
        <button id="submitInviteBtn" class="w-full bg-emerald-600 hover:bg-emerald-500 text-white font-semibold py-3 rounded-xl transition-colors">Enregistrer l'invité</button>
      </div>
    </div>
  </div>

  <div id="toastContainer" class="fixed bottom-4 right-4 z-[60] flex flex-col gap-2 max-w-sm"></div>

  <script src="https://cdn.sheetjs.com/xlsx-0.20.3/package/dist/xlsx.full.min.js"></script>
  <script>
    window.ADMIN_CONFIG = {
      baseUrl: '/welcomy/Backend/controllers',
      whatsappTemplate: <?= json_encode($WHATSAPP_TEMPLATE, JSON_UNESCAPED_UNICODE) ?>,
      supportPhone: <?= json_encode($WHATSAPP_SUPPORT_NUMBER, JSON_UNESCAPED_UNICODE) ?>
    };
  </script>
  <script src="/welcomy/Backend/js/phone_utils.js"></script>
  <script src="/welcomy/Backend/js/admin_dashboard.js"></script>
</body>
</html>
