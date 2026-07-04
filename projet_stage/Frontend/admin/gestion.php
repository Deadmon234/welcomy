<?php
require_once __DIR__ . '/../../../Backend/config/auth.php';
if (!isset($_SESSION['user_id'], $_SESSION['role']) || welcomy_current_role() !== 'admin') {
    header('Location: ../login.php');
    exit;
}
$adminName = htmlspecialchars($_SESSION['nom'], ENT_QUOTES, 'UTF-8');
$currentUserId = (int)$_SESSION['user_id'];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>WELCOMY — Gestion événements & utilisateurs</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <script>tailwind.config={theme:{extend:{fontFamily:{sans:['Inter','system-ui','sans-serif']},animation:{'fade-in':'fadeIn .3s ease-out'},keyframes:{fadeIn:{from:{opacity:0,transform:'translateY(8px)'},to:{opacity:1,transform:'translateY(0)'}}}}}};</script>
  <style>body{font-family:'Inter',system-ui,sans-serif}.glass{background:rgba(30,41,59,.7);backdrop-filter:blur(12px)}</style>
</head>
<body class="bg-slate-950 text-slate-100 min-h-screen">

  <header class="sticky top-0 z-40 border-b border-slate-800/80 glass">
    <div class="max-w-6xl mx-auto px-4 py-4 flex flex-col lg:flex-row items-start lg:items-center justify-between gap-4">
      <div class="flex items-center gap-3">
        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-indigo-600 to-violet-800 flex items-center justify-center">
          <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
        </div>
        <div>
          <h1 class="text-lg font-bold text-white">Gestion</h1>
          <p class="text-xs text-slate-400">Événements & utilisateurs · <?= $adminName ?></p>
        </div>
      </div>
      <div class="flex flex-wrap items-center gap-2">
        <a href="dashboardOrganisateur.php" class="inline-flex items-center gap-2 bg-slate-800 hover:bg-slate-700 border border-slate-700 px-4 py-2.5 rounded-xl text-sm font-medium text-slate-300">Dashboard</a>
        <a href="verification_evenement.php" class="inline-flex items-center gap-2 bg-slate-800 hover:bg-slate-700 border border-slate-700 px-4 py-2.5 rounded-xl text-sm font-medium text-slate-300">Vérifier présences</a>
        <form action="../logout.php" method="POST">
          <button type="submit" class="inline-flex items-center gap-2 bg-slate-800 hover:bg-slate-700 border border-slate-700 px-4 py-2.5 rounded-xl text-sm font-medium text-slate-300">Déconnexion</button>
        </form>
      </div>
    </div>
  </header>

  <main class="max-w-6xl mx-auto px-4 py-6 space-y-6">
    <div class="flex gap-2 p-1 bg-slate-900/60 border border-slate-800 rounded-xl w-full sm:w-auto">
      <button id="tabEvents" class="tab-btn flex-1 sm:flex-none px-5 py-2.5 rounded-lg text-sm font-semibold bg-indigo-600 text-white">Événements</button>
      <button id="tabUsers" class="tab-btn flex-1 sm:flex-none px-5 py-2.5 rounded-lg text-sm font-semibold text-slate-400 hover:text-white">Utilisateurs</button>
    </div>

    <!-- Événements -->
    <section id="panelEvents" class="space-y-4">
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
          <h2 class="text-xl font-bold text-white">Vos événements</h2>
          <p class="text-sm text-slate-400 mt-1">Créez, consultez et supprimez vos événements.</p>
        </div>
        <button id="openCreateEvent" class="inline-flex items-center justify-center gap-2 bg-indigo-600 hover:bg-indigo-500 px-4 py-2.5 rounded-xl text-sm font-semibold text-white">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
          Nouvel événement
        </button>
      </div>
      <div id="eventsList" class="space-y-3"></div>
      <div id="eventsEmpty" class="hidden text-center py-16 text-slate-400 text-sm">Aucun événement pour le moment.</div>
    </section>

    <!-- Utilisateurs -->
    <section id="panelUsers" class="hidden space-y-4">
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
          <h2 class="text-xl font-bold text-white">Utilisateurs</h2>
          <p class="text-sm text-slate-400 mt-1">Gérez les administrateurs et les hôtesses.</p>
        </div>
        <button id="openCreateUser" class="inline-flex items-center justify-center gap-2 bg-violet-600 hover:bg-violet-500 px-4 py-2.5 rounded-xl text-sm font-semibold text-white">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
          Nouvel utilisateur
        </button>
      </div>
      <div id="usersList" class="space-y-3"></div>
      <div id="usersEmpty" class="hidden text-center py-16 text-slate-400 text-sm">Aucun utilisateur.</div>
    </section>
  </main>

  <!-- Modal créer événement -->
  <div id="createEventModal" class="fixed inset-0 hidden z-50 flex items-end sm:items-center justify-center bg-black/60 backdrop-blur-sm p-4">
    <div class="w-full max-w-lg bg-slate-900 border border-slate-700 rounded-2xl shadow-2xl animate-fade-in">
      <div class="p-6 border-b border-slate-800 flex items-center justify-between">
        <div><h3 class="text-lg font-semibold text-white">Créer un événement</h3><p class="text-sm text-slate-400">Renseignez les informations</p></div>
        <button id="cancelCreateEvent" class="text-slate-500 hover:text-white p-1"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
      </div>
      <div class="p-6 space-y-4">
        <input id="eventTitleInput" placeholder="Titre de l'événement" class="w-full bg-slate-800 border border-slate-700 rounded-xl px-4 py-3 text-sm text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-indigo-500" />
        <input id="eventDateInput" type="datetime-local" class="w-full bg-slate-800 border border-slate-700 rounded-xl px-4 py-3 text-sm text-white focus:outline-none focus:ring-2 focus:ring-indigo-500" />
        <input id="eventLocationInput" placeholder="Lieu" class="w-full bg-slate-800 border border-slate-700 rounded-xl px-4 py-3 text-sm text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-indigo-500" />
        <textarea id="eventDescriptionInput" placeholder="Description (optionnel)" rows="3" class="w-full bg-slate-800 border border-slate-700 rounded-xl px-4 py-3 text-sm text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-indigo-500"></textarea>
        <button id="createEventBtn" class="w-full bg-indigo-600 hover:bg-indigo-500 text-white font-semibold py-3 rounded-xl">Créer l'événement</button>
      </div>
    </div>
  </div>

  <!-- Modal créer utilisateur -->
  <div id="createUserModal" class="fixed inset-0 hidden z-50 flex items-end sm:items-center justify-center bg-black/60 backdrop-blur-sm p-4">
    <div class="w-full max-w-lg bg-slate-900 border border-slate-700 rounded-2xl shadow-2xl animate-fade-in">
      <div class="p-6 border-b border-slate-800 flex items-center justify-between">
        <div><h3 class="text-lg font-semibold text-white">Nouvel utilisateur</h3><p class="text-sm text-slate-400">Créer un administrateur ou une hôtesse</p></div>
        <button id="cancelCreateUser" class="text-slate-500 hover:text-white p-1"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
      </div>
      <div class="p-6 space-y-4">
        <input id="userNameInput" placeholder="Nom complet" class="w-full bg-slate-800 border border-slate-700 rounded-xl px-4 py-3 text-sm text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-violet-500" />
        <input id="userEmailInput" type="email" placeholder="Adresse email" class="w-full bg-slate-800 border border-slate-700 rounded-xl px-4 py-3 text-sm text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-violet-500" />
        <input id="userPasswordInput" type="password" placeholder="Mot de passe" class="w-full bg-slate-800 border border-slate-700 rounded-xl px-4 py-3 text-sm text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-violet-500" />
        <select id="userRoleInput" class="w-full bg-slate-800 border border-slate-700 rounded-xl px-4 py-3 text-sm text-white focus:outline-none focus:ring-2 focus:ring-violet-500">
          <option value="hotesse">Hôtesse</option>
          <option value="admin">Administrateur</option>
        </select>
        <button id="createUserBtn" class="w-full bg-violet-600 hover:bg-violet-500 text-white font-semibold py-3 rounded-xl">Créer l'utilisateur</button>
      </div>
    </div>
  </div>

  <div id="toastContainer" class="fixed bottom-4 right-4 z-[60] flex flex-col gap-2 max-w-sm"></div>

  <script>
    window.GESTION_CONFIG = {
      baseUrl: '/welcomy/Backend/controllers',
      currentUserId: <?= $currentUserId ?>
    };
  </script>
  <script src="/welcomy/Backend/js/admin_gestion.js"></script>
</body>
</html>
