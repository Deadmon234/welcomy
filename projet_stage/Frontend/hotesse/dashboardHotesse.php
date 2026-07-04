<?php
session_start();
if (!isset($_SESSION['user_id'], $_SESSION['nom'], $_SESSION['role']) || $_SESSION['role'] !== 'hotesse') {
    header('Location: ../login.php');
    exit;
}

require_once __DIR__ . '/../../../Backend/config/notification_config.php';
$hostessName = htmlspecialchars($_SESSION['nom'], ENT_QUOTES, 'UTF-8');
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>WELCOMY — Espace Hôtesse</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <script>
    tailwind.config = {
      theme: {
        extend: {
          fontFamily: { sans: ['Inter', 'system-ui', 'sans-serif'] },
          animation: { 'fade-in': 'fadeIn .3s ease-out' },
          keyframes: { fadeIn: { from: { opacity: 0, transform: 'translateY(8px)' }, to: { opacity: 1, transform: 'translateY(0)' } } }
        }
      }
    };
  </script>
  <style>
    body { font-family: 'Inter', system-ui, sans-serif; }
    .glass { background: rgba(30, 41, 59, 0.7); backdrop-filter: blur(12px); }
  </style>
</head>
<body class="bg-slate-950 text-slate-100 min-h-screen">

  <!-- Header -->
  <header class="sticky top-0 z-40 border-b border-slate-800/80 glass">
    <div class="max-w-6xl mx-auto px-4 py-4 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
      <div class="flex items-center gap-3">
        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-violet-600 to-violet-800 flex items-center justify-center shadow-lg shadow-violet-900/30">
          <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
          </svg>
        </div>
        <div>
          <h1 class="text-lg font-bold text-white tracking-tight">WELCOMY</h1>
          <p class="text-xs text-slate-400">Espace hôtesse · Bonjour, <?= $hostessName ?></p>
        </div>
      </div>
      <div class="flex items-center gap-2 w-full sm:w-auto">
        <button id="openInviteModal" class="flex-1 sm:flex-none inline-flex items-center justify-center gap-2 bg-violet-600 hover:bg-violet-500 px-4 py-2.5 rounded-xl text-sm font-semibold transition-colors">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
          Ajouter un invité
        </button>
        <form action="../logout.php" method="POST">
          <button type="submit" class="inline-flex items-center gap-2 bg-slate-800 hover:bg-slate-700 border border-slate-700 px-4 py-2.5 rounded-xl text-sm font-medium text-slate-300 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
            Déconnexion
          </button>
        </form>
      </div>
    </div>
  </header>

  <main class="max-w-6xl mx-auto px-4 py-6 space-y-6">

    <!-- Event selector -->
    <section class="bg-slate-900/50 border border-slate-800 rounded-2xl p-5">
      <label for="eventSelect" class="block text-sm font-medium text-slate-400 mb-2">Événement en cours</label>
      <select id="eventSelect" class="w-full bg-slate-800 border border-slate-700 text-white rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-violet-500 focus:border-transparent transition">
        <option value="" disabled selected>— Choisir un événement —</option>
      </select>

      <div id="eventInfoCard" class="hidden mt-4 pt-4 border-t border-slate-800">
        <h2 id="eventTitle" class="text-lg font-semibold text-white"></h2>
        <div id="eventMeta" class="mt-2 flex flex-col sm:flex-row gap-3 text-sm text-slate-400"></div>
      </div>
    </section>

    <!-- Stats -->
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

    <!-- Search & filters -->
    <section class="flex flex-col sm:flex-row gap-3">
      <div class="relative flex-1">
        <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
        </svg>
        <input id="search" type="text" placeholder="Rechercher par nom, téléphone ou email…"
          class="w-full bg-slate-900/50 border border-slate-800 rounded-xl pl-10 pr-4 py-3 text-sm text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-violet-500 transition" />
      </div>
      <div class="flex gap-2">
        <button data-filter="all" class="filter-btn px-4 py-3 rounded-xl text-sm font-medium bg-violet-600 text-white transition-colors">Tous</button>
        <button data-filter="present" class="filter-btn px-4 py-3 rounded-xl text-sm font-medium bg-slate-800 text-slate-400 transition-colors">Présents</button>
        <button data-filter="absent" class="filter-btn px-4 py-3 rounded-xl text-sm font-medium bg-slate-800 text-slate-400 transition-colors">En attente</button>
      </div>
    </section>

    <!-- Guest list -->
    <section>
      <div id="guestList" class="space-y-3"></div>
      <div id="emptyState" class="text-center py-16">
        <div class="w-16 h-16 mx-auto mb-4 rounded-2xl bg-slate-800 flex items-center justify-center">
          <svg class="w-8 h-8 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
          </svg>
        </div>
        <p class="text-slate-400 text-sm">Sélectionnez un événement pour afficher la liste des invités.</p>
      </div>
    </section>
  </main>

  <!-- WhatsApp confirmation modal -->
  <div id="whatsappModal" class="fixed inset-0 hidden z-50 flex items-end sm:items-center justify-center bg-black/60 backdrop-blur-sm p-4">
    <div class="w-full max-w-lg bg-slate-900 border border-slate-700 rounded-2xl shadow-2xl animate-fade-in">
      <div class="p-6 border-b border-slate-800">
        <div class="flex items-center gap-3">
          <div class="w-10 h-10 rounded-full bg-emerald-500/20 flex items-center justify-center">
            <svg class="w-5 h-5 text-emerald-400" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/></svg>
          </div>
          <div>
            <h3 class="text-lg font-semibold text-white">Confirmer la présence</h3>
            <p class="text-sm text-slate-400">Un message WhatsApp sera préparé pour l'invité</p>
          </div>
        </div>
      </div>
      <div class="p-6 space-y-4">
        <div class="flex items-center gap-3 bg-slate-800/50 rounded-xl p-3">
          <div class="min-w-0 flex-1">
            <p id="whatsappGuestName" class="font-semibold text-white truncate"></p>
            <p id="whatsappGuestPhone" class="text-sm text-slate-400"></p>
          </div>
        </div>
        <div>
          <p class="text-xs font-medium text-slate-500 uppercase tracking-wider mb-2">Aperçu du message</p>
          <div id="whatsappPreview" class="bg-[#075e54]/10 border border-emerald-900/30 rounded-xl p-4 text-sm text-slate-300 whitespace-pre-wrap leading-relaxed max-h-48 overflow-y-auto"></div>
        </div>
        <div id="whatsappPhoneWarning" class="hidden bg-amber-500/10 border border-amber-500/30 rounded-xl p-3 text-sm text-amber-400">
          ⚠ Aucun numéro de téléphone renseigné. La présence sera enregistrée sans envoi WhatsApp.
        </div>
      </div>
      <div class="p-6 pt-0 flex flex-col sm:flex-row gap-3">
        <button id="whatsappCancelBtn" class="flex-1 px-4 py-3 rounded-xl text-sm font-medium bg-slate-800 hover:bg-slate-700 text-slate-300 transition-colors">
          Annuler
        </button>
        <button id="whatsappConfirmBtn" class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-3 rounded-xl text-sm font-semibold bg-emerald-600 hover:bg-emerald-500 text-white transition-colors">
          Marquer présent et ouvrir WhatsApp
        </button>
      </div>
    </div>
  </div>

  <!-- Add invite modal -->
  <div id="inviteModal" class="fixed inset-0 hidden z-50 flex items-end sm:items-center justify-center bg-black/60 backdrop-blur-sm p-4">
    <div class="w-full max-w-md bg-slate-900 border border-slate-700 rounded-2xl shadow-2xl animate-fade-in">
      <div class="p-6 border-b border-slate-800 flex items-center justify-between">
        <div>
          <h3 class="text-lg font-semibold text-white">Nouvel invité</h3>
          <p class="text-sm text-slate-400">Inscrire un participant à l'événement</p>
        </div>
        <button id="cancelInviteModal" class="text-slate-500 hover:text-white transition-colors p-1">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
      </div>
      <form id="inviteForm" class="p-6 space-y-4">
        <div>
          <label class="block text-xs font-medium text-slate-400 mb-1.5">Nom complet *</label>
          <input type="text" name="nom" required placeholder="Ex. Jean Dupont"
            class="w-full bg-slate-800 border border-slate-700 rounded-xl px-4 py-3 text-sm text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-violet-500">
        </div>
        <div>
          <label class="block text-xs font-medium text-slate-400 mb-1.5">Téléphone (WhatsApp) *</label>
          <input type="tel" name="telephone" required placeholder="Ex. +237 6XX XXX XXX"
            class="w-full bg-slate-800 border border-slate-700 rounded-xl px-4 py-3 text-sm text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-violet-500">
        </div>
        <div>
          <label class="block text-xs font-medium text-slate-400 mb-1.5">Email (optionnel)</label>
          <input type="email" name="email" placeholder="email@exemple.com"
            class="w-full bg-slate-800 border border-slate-700 rounded-xl px-4 py-3 text-sm text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-violet-500">
        </div>
        <div>
          <label class="block text-xs font-medium text-slate-400 mb-1.5">Événement *</label>
          <select id="eventSelectForm" name="event_id" required
            class="w-full bg-slate-800 border border-slate-700 rounded-xl px-4 py-3 text-sm text-white focus:outline-none focus:ring-2 focus:ring-violet-500">
            <option value="">— Choisir un événement —</option>
          </select>
        </div>
        <div id="inviteMessage" class="text-sm"></div>
        <button type="submit" class="w-full bg-violet-600 hover:bg-violet-500 text-white font-semibold py-3 rounded-xl transition-colors">
          Enregistrer l'invité
        </button>
      </form>
    </div>
  </div>

  <!-- Toast container -->
  <div id="toastContainer" class="fixed bottom-4 right-4 z-[60] flex flex-col gap-2 max-w-sm"></div>

  <script>
    window.HOSTESS_CONFIG = {
      baseUrl: '/welcomy/Backend/controllers',
      whatsappTemplate: <?= json_encode($WHATSAPP_TEMPLATE, JSON_UNESCAPED_UNICODE) ?>,
      supportPhone: <?= json_encode($WHATSAPP_SUPPORT_NUMBER, JSON_UNESCAPED_UNICODE) ?>
    };
  </script>
  <script src="/welcomy/Backend/js/hostess_dashboard.js"></script>
</body>
</html>
