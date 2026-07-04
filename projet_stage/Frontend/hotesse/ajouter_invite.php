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
  <title>WELCOMY — Ajouter un invité</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <script>tailwind.config={theme:{extend:{fontFamily:{sans:['Inter','system-ui','sans-serif']},animation:{'fade-in':'fadeIn .3s ease-out'},keyframes:{fadeIn:{from:{opacity:0,transform:'translateY(8px)'},to:{opacity:1,transform:'translateY(0)'}}}}}};</script>
  <style>body{font-family:'Inter',system-ui,sans-serif}</style>
</head>
<body class="bg-slate-950 text-slate-100 min-h-screen flex flex-col">

  <header class="border-b border-slate-800 bg-slate-900/50">
    <div class="max-w-lg mx-auto px-4 py-4 flex items-center justify-between">
      <div class="flex items-center gap-3">
        <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-violet-600 to-violet-800 flex items-center justify-center">
          <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        </div>
        <div>
          <h1 class="text-base font-bold text-white">Nouvel invité</h1>
          <p class="text-xs text-slate-400"><?= $hostessName ?></p>
        </div>
      </div>
      <a href="dashboardHotesse.php" class="text-sm text-violet-400 hover:text-violet-300 font-medium">← Retour</a>
    </div>
  </header>

  <main class="flex-1 flex items-start justify-center px-4 py-8">
    <div class="w-full max-w-lg animate-fade-in">
      <div class="bg-slate-900/50 border border-slate-800 rounded-2xl p-6 shadow-xl">
        <p class="text-sm text-slate-400 mb-6">Inscrivez un participant à un événement. Le numéro WhatsApp permettra d'envoyer la confirmation de présence.</p>

        <form id="inviteForm" class="space-y-4">
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
          <div id="message" class="text-sm"></div>
          <button type="submit" id="submitBtn" class="w-full bg-violet-600 hover:bg-violet-500 text-white font-semibold py-3 rounded-xl transition-colors">
            Enregistrer l'invité
          </button>
        </form>
      </div>
    </div>
  </main>

  <div id="toastContainer" class="fixed bottom-4 right-4 z-50 flex flex-col gap-2 max-w-sm"></div>

  <script>
    const baseUrl = '/welcomy/Backend/controllers';

    function showToast(msg, type = 'success') {
      const colors = { success: 'bg-emerald-600', error: 'bg-red-600' };
      const t = document.createElement('div');
      t.className = `${colors[type] || colors.success} text-white px-4 py-3 rounded-xl shadow-lg text-sm font-medium animate-fade-in`;
      t.textContent = msg;
      document.getElementById('toastContainer').appendChild(t);
      setTimeout(() => t.remove(), 3500);
    }

    function formatDate(d) {
      if (!d) return '';
      const dt = new Date(String(d).replace(' ', 'T'));
      return isNaN(dt.getTime()) ? d : dt.toLocaleDateString('fr-FR', { day: 'numeric', month: 'short', year: 'numeric' });
    }

    document.addEventListener('DOMContentLoaded', async () => {
      const sel = document.getElementById('eventSelectForm');
      try {
        const resp = await fetch(`${baseUrl}/get_eventsController.php`, { credentials: 'same-origin' });
        const data = await resp.json();
        (Array.isArray(data) ? data : []).forEach(ev => {
          const opt = document.createElement('option');
          opt.value = ev.id_even;
          opt.textContent = `${ev.title} — ${formatDate(ev.event_date)}`;
          sel.appendChild(opt);
        });
      } catch (e) { console.error(e); }

      document.getElementById('inviteForm').addEventListener('submit', async e => {
        e.preventDefault();
        const btn = document.getElementById('submitBtn');
        const msg = document.getElementById('message');
        btn.disabled = true;
        msg.textContent = '';
        try {
          const res = await fetch(`${baseUrl}/create_inviteController.php`, {
            method: 'POST', credentials: 'same-origin',
            body: new FormData(e.target)
          });
          const data = await res.json();
          if (data.status === 'success') {
            showToast('Invité ajouté avec succès.');
            setTimeout(() => { window.location.href = 'dashboardHotesse.php'; }, 1200);
          } else {
            msg.innerHTML = `<span class="text-red-400">${data.message || 'Erreur.'}</span>`;
          }
        } catch {
          msg.innerHTML = '<span class="text-red-400">Erreur de connexion.</span>';
        } finally {
          btn.disabled = false;
        }
      });
    });
  </script>
</body>
</html>
