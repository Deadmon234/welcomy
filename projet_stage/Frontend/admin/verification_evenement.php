<?php
session_start();
if (!isset($_SESSION['user_id'], $_SESSION['nom'], $_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}
require_once __DIR__ . '/../../../Backend/config/notification_config.php';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>WELCOMY — Vérification des présences</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <script>tailwind.config={theme:{extend:{fontFamily:{sans:['Inter','system-ui','sans-serif']},animation:{'fade-in':'fadeIn .3s ease-out'},keyframes:{fadeIn:{from:{opacity:0,transform:'translateY(8px)'},to:{opacity:1,transform:'translateY(0)'}}}}}};</script>
  <style>body{font-family:'Inter',system-ui,sans-serif}.glass{background:rgba(30,41,59,.7);backdrop-filter:blur(12px)}</style>
</head>
<body class="bg-slate-950 text-slate-100 min-h-screen">

  <header class="sticky top-0 z-40 border-b border-slate-800/80 glass">
    <div class="max-w-6xl mx-auto px-4 py-4 flex items-center justify-between gap-4">
      <div class="flex items-center gap-3">
        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-emerald-600 to-emerald-800 flex items-center justify-center">
          <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <div>
          <h1 class="text-lg font-bold text-white">Vérification des présences</h1>
          <p class="text-xs text-slate-400">Validez les invités et envoyez la confirmation WhatsApp</p>
        </div>
      </div>
      <div class="flex flex-wrap items-center gap-2">
        <a href="dashboardOrganisateur.php" class="inline-flex items-center gap-2 bg-slate-800 hover:bg-slate-700 border border-slate-700 px-4 py-2.5 rounded-xl text-sm font-medium text-slate-300">Dashboard</a>
        <a href="gestion.php" class="inline-flex items-center gap-2 bg-slate-800 hover:bg-slate-700 border border-slate-700 px-4 py-2.5 rounded-xl text-sm font-medium text-slate-300">Gestion</a>
      </div>
    </div>
  </header>

  <main class="max-w-6xl mx-auto px-4 py-6 space-y-6">
    <section class="bg-slate-900/50 border border-slate-800 rounded-2xl p-5">
      <label for="eventSelect" class="block text-sm font-medium text-slate-400 mb-2">Événement</label>
      <select id="eventSelect" class="w-full bg-slate-800 border border-slate-700 text-white rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
        <option value="" disabled selected>— Choisir un événement —</option>
      </select>
    </section>

    <section class="grid grid-cols-3 gap-3 sm:gap-4">
      <div class="bg-slate-900/50 border border-slate-800 rounded-2xl p-4 text-center">
        <p id="statTotal" class="text-2xl sm:text-3xl font-bold text-white">0</p>
        <p class="text-xs sm:text-sm text-slate-400 mt-1">Invités</p>
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

    <section>
      <div id="guestList" class="space-y-3"></div>
      <div id="emptyState" class="text-center py-16">
        <p class="text-slate-400 text-sm">Sélectionnez un événement pour afficher les invités.</p>
      </div>
    </section>
  </main>

  <div id="whatsappModal" class="fixed inset-0 hidden z-50 flex items-end sm:items-center justify-center bg-black/60 backdrop-blur-sm p-4">
    <div class="w-full max-w-lg bg-slate-900 border border-slate-700 rounded-2xl shadow-2xl animate-fade-in">
      <div class="p-6 border-b border-slate-800">
        <h3 class="text-lg font-semibold text-white">Valider la présence</h3>
        <p class="text-sm text-slate-400 mt-1">Confirmez et envoyez le message WhatsApp</p>
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
        <div id="whatsappPhoneWarning" class="hidden bg-amber-500/10 border border-amber-500/30 rounded-xl p-3 text-sm text-amber-400">⚠ Aucun numéro renseigné.</div>
      </div>
      <div class="p-6 pt-0 flex flex-col sm:flex-row gap-3">
        <button id="whatsappCancelBtn" class="flex-1 px-4 py-3 rounded-xl text-sm font-medium bg-slate-800 hover:bg-slate-700 text-slate-300">Annuler</button>
        <button id="whatsappConfirmBtn" class="flex-1 px-4 py-3 rounded-xl text-sm font-semibold bg-emerald-600 hover:bg-emerald-500 text-white">Valider et ouvrir WhatsApp</button>
      </div>
    </div>
  </div>

  <div id="thankyouModal" class="fixed inset-0 hidden z-50 flex items-end sm:items-center justify-center bg-black/60 backdrop-blur-sm p-4">
    <div class="w-full max-w-lg bg-slate-900 border border-slate-700 rounded-2xl shadow-2xl animate-fade-in">
      <div class="p-6 border-b border-slate-800">
        <h3 class="text-lg font-semibold text-white">Remercier l'invité</h3>
        <p class="text-sm text-slate-400 mt-1">Envoyez un message de remerciement via WhatsApp</p>
      </div>
      <div class="p-6 space-y-4">
        <div class="bg-slate-800/50 rounded-xl p-3">
          <p id="thankyouGuestName" class="font-semibold text-white"></p>
          <p id="thankyouGuestPhone" class="text-sm text-slate-400"></p>
        </div>
        <div>
          <p class="text-xs font-medium text-slate-500 uppercase tracking-wider mb-2">Message de remerciement</p>
          <div id="thankyouPreview" class="bg-[#075e54]/10 border border-emerald-900/30 rounded-xl p-4 text-sm text-slate-300 whitespace-pre-wrap max-h-48 overflow-y-auto"></div>
        </div>
        <div id="thankyouPhoneWarning" class="hidden bg-amber-500/10 border border-amber-500/30 rounded-xl p-3 text-sm text-amber-400">⚠ Aucun numéro renseigné — impossible d'ouvrir WhatsApp.</div>
      </div>
      <div class="p-6 pt-0 flex flex-col sm:flex-row gap-3">
        <button id="thankyouCancelBtn" class="flex-1 px-4 py-3 rounded-xl text-sm font-medium bg-slate-800 hover:bg-slate-700 text-slate-300">Annuler</button>
        <button id="thankyouConfirmBtn" class="flex-1 px-4 py-3 rounded-xl text-sm font-semibold bg-emerald-600 hover:bg-emerald-500 text-white">Ouvrir WhatsApp</button>
      </div>
    </div>
  </div>

  <div id="toastContainer" class="fixed bottom-4 right-4 z-[60] flex flex-col gap-2 max-w-sm"></div>

  <script>
    window.ADMIN_CONFIG = {
      baseUrl: '/welcomy/Backend/controllers',
      whatsappTemplate: <?= json_encode($WHATSAPP_TEMPLATE, JSON_UNESCAPED_UNICODE) ?>,
      thankyouTemplate: <?= json_encode($WHATSAPP_THANKYOU_TEMPLATE, JSON_UNESCAPED_UNICODE) ?>,
      supportPhone: <?= json_encode($WHATSAPP_SUPPORT_NUMBER, JSON_UNESCAPED_UNICODE) ?>
    };
  </script>
  <script src="/welcomy/Backend/js/admin_verification.js"></script>
</body>
</html>
