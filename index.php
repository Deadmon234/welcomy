<?php
require_once __DIR__ . '/Backend/config/auth.php';
if (!empty($_SESSION['user_id'])) {
    welcomy_redirect_if_logged_in();
}
$loginUrl = 'projet_stage/Frontend/login.php';
$registerUrl = 'projet_stage/Frontend/register.php';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>WELCOMY — Gestion d'événements & accueil invités</title>
  <meta name="description" content="WELCOMY simplifie l'organisation de vos événements : invités, hôtesses, présences et notifications WhatsApp avec Asso+.">
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <script>
    tailwind.config = {
      theme: {
        extend: {
          fontFamily: { sans: ['Inter', 'system-ui', 'sans-serif'] },
          animation: {
            'fade-up': 'fadeUp .6s ease-out both',
            'fade-up-delay': 'fadeUp .6s ease-out .15s both',
            'float': 'float 6s ease-in-out infinite'
          },
          keyframes: {
            fadeUp: { from: { opacity: 0, transform: 'translateY(20px)' }, to: { opacity: 1, transform: 'translateY(0)' } },
            float: { '0%, 100%': { transform: 'translateY(0)' }, '50%': { transform: 'translateY(-10px)' } }
          }
        }
      }
    };
  </script>
  <style>
    body { font-family: 'Inter', system-ui, sans-serif; }
    .hero-glow {
      background:
        radial-gradient(ellipse 80% 60% at 50% -10%, rgba(16,185,129,.18), transparent),
        radial-gradient(ellipse 50% 40% at 90% 20%, rgba(139,92,246,.12), transparent),
        radial-gradient(ellipse 40% 30% at 10% 60%, rgba(59,130,246,.08), transparent);
    }
    .glass { background: rgba(15,23,42,.65); backdrop-filter: blur(14px); }
    .card-hover { transition: transform .25s ease, border-color .25s ease, box-shadow .25s ease; }
    .card-hover:hover { transform: translateY(-4px); border-color: rgba(16,185,129,.35); box-shadow: 0 20px 40px -12px rgba(16,185,129,.15); }
  </style>
</head>
<body class="bg-slate-950 text-slate-100 min-h-screen antialiased">

  <!-- Navigation -->
  <nav class="fixed top-0 inset-x-0 z-50 border-b border-slate-800/60 glass">
    <div class="max-w-6xl mx-auto px-4 h-16 flex items-center justify-between gap-4">
      <a href="index.php" class="flex items-center gap-2.5 group">
        <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-emerald-500 to-emerald-700 flex items-center justify-center shadow-lg shadow-emerald-900/30 group-hover:scale-105 transition-transform">
          <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/></svg>
        </div>
        <span class="text-lg font-bold tracking-tight text-white">WELCOMY</span>
      </a>
      <div class="flex items-center gap-2 sm:gap-3">
        <a href="<?= htmlspecialchars($loginUrl) ?>" class="hidden sm:inline-flex px-4 py-2 rounded-xl text-sm font-medium text-slate-300 hover:text-white hover:bg-slate-800 transition-colors">Connexion</a>
        <a href="<?= htmlspecialchars($registerUrl) ?>" class="inline-flex px-4 py-2.5 rounded-xl text-sm font-semibold bg-emerald-600 hover:bg-emerald-500 text-white transition-colors shadow-lg shadow-emerald-900/25">Commencer</a>
      </div>
    </div>
  </nav>

  <!-- Hero -->
  <section class="relative pt-28 pb-20 sm:pt-36 sm:pb-28 hero-glow overflow-hidden">
    <div class="absolute inset-0 pointer-events-none">
      <div class="absolute top-32 right-[10%] w-64 h-64 rounded-full bg-emerald-500/5 blur-3xl animate-float"></div>
      <div class="absolute bottom-20 left-[5%] w-48 h-48 rounded-full bg-violet-500/5 blur-3xl animate-float" style="animation-delay:-3s"></div>
    </div>

    <div class="max-w-6xl mx-auto px-4 relative">
      <div class="max-w-3xl animate-fade-up">
        <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 mb-6">
          <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
          Plateforme Asso+ · Événements & accueil
        </span>
        <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold text-white leading-[1.1] tracking-tight">
          Accueillez vos invités<br>
          <span class="bg-gradient-to-r from-emerald-400 to-teal-300 bg-clip-text text-transparent">avec élégance</span>
        </h1>
        <p class="mt-6 text-lg sm:text-xl text-slate-400 leading-relaxed max-w-2xl">
          WELCOMY centralise la gestion de vos événements : listes d'invités, équipes d'hôtesses, vérification des présences et envoi de messages WhatsApp en un seul endroit.
        </p>
        <div class="mt-10 flex flex-col sm:flex-row gap-4 animate-fade-up-delay">
          <a href="<?= htmlspecialchars($loginUrl) ?>" class="inline-flex items-center justify-center gap-2 px-7 py-3.5 rounded-2xl text-base font-semibold bg-emerald-600 hover:bg-emerald-500 text-white transition-all hover:scale-[1.02] shadow-xl shadow-emerald-900/30">
            Se connecter
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
          </a>
          <a href="<?= htmlspecialchars($registerUrl) ?>" class="inline-flex items-center justify-center gap-2 px-7 py-3.5 rounded-2xl text-base font-semibold bg-slate-800 hover:bg-slate-700 border border-slate-700 text-slate-200 transition-colors">
            Créer un compte hôtesse
          </a>
        </div>
      </div>

      <!-- Stats preview -->
      <div class="mt-16 grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 animate-fade-up-delay">
        <div class="bg-slate-900/60 border border-slate-800 rounded-2xl p-4 sm:p-5 text-center">
          <p class="text-2xl sm:text-3xl font-bold text-white">100%</p>
          <p class="text-xs sm:text-sm text-slate-400 mt-1">Présences tracées</p>
        </div>
        <div class="bg-slate-900/60 border border-slate-800 rounded-2xl p-4 sm:p-5 text-center">
          <p class="text-2xl sm:text-3xl font-bold text-emerald-400">WhatsApp</p>
          <p class="text-xs sm:text-sm text-slate-400 mt-1">Confirmations & remerciements</p>
        </div>
        <div class="bg-slate-900/60 border border-slate-800 rounded-2xl p-4 sm:p-5 text-center">
          <p class="text-2xl sm:text-3xl font-bold text-white">Multi</p>
          <p class="text-xs sm:text-sm text-slate-400 mt-1">Organisateurs & hôtesses</p>
        </div>
        <div class="bg-slate-900/60 border border-slate-800 rounded-2xl p-4 sm:p-5 text-center">
          <p class="text-2xl sm:text-3xl font-bold text-violet-400">Asso+</p>
          <p class="text-xs sm:text-sm text-slate-400 mt-1">Intégration native</p>
        </div>
      </div>
    </div>
  </section>

  <!-- Features -->
  <section class="py-20 border-t border-slate-800/60">
    <div class="max-w-6xl mx-auto px-4">
      <div class="text-center max-w-2xl mx-auto mb-14">
        <h2 class="text-3xl sm:text-4xl font-bold text-white">Tout ce dont vous avez besoin</h2>
        <p class="mt-4 text-slate-400">De la création de l'événement au remerciement final, WELCOMY accompagne chaque étape.</p>
      </div>

      <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-5">
        <article class="card-hover bg-slate-900/50 border border-slate-800 rounded-2xl p-6">
          <div class="w-11 h-11 rounded-xl bg-violet-500/15 flex items-center justify-center mb-4">
            <svg class="w-6 h-6 text-violet-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
          </div>
          <h3 class="text-lg font-semibold text-white">Événements</h3>
          <p class="mt-2 text-sm text-slate-400 leading-relaxed">Créez et gérez vos événements : date, lieu, description et liste d'invités associée.</p>
        </article>

        <article class="card-hover bg-slate-900/50 border border-slate-800 rounded-2xl p-6">
          <div class="w-11 h-11 rounded-xl bg-emerald-500/15 flex items-center justify-center mb-4">
            <svg class="w-6 h-6 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
          </div>
          <h3 class="text-lg font-semibold text-white">Présences</h3>
          <p class="mt-2 text-sm text-slate-400 leading-relaxed">Validez les arrivées en temps réel et consultez les statistiques présents / en attente.</p>
        </article>

        <article class="card-hover bg-slate-900/50 border border-slate-800 rounded-2xl p-6">
          <div class="w-11 h-11 rounded-xl bg-sky-500/15 flex items-center justify-center mb-4">
            <svg class="w-6 h-6 text-sky-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
          </div>
          <h3 class="text-lg font-semibold text-white">Hôtesses</h3>
          <p class="mt-2 text-sm text-slate-400 leading-relaxed">Déployez votre équipe sur le terrain pour enregistrer les invités depuis leur interface dédiée.</p>
        </article>

        <article class="card-hover bg-slate-900/50 border border-slate-800 rounded-2xl p-6">
          <div class="w-11 h-11 rounded-xl bg-green-500/15 flex items-center justify-center mb-4">
            <svg class="w-6 h-6 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
          </div>
          <h3 class="text-lg font-semibold text-white">WhatsApp</h3>
          <p class="mt-2 text-sm text-slate-400 leading-relaxed">Envoyez confirmations de présence et messages de remerciement personnalisés en un clic.</p>
        </article>

        <article class="card-hover bg-slate-900/50 border border-slate-800 rounded-2xl p-6">
          <div class="w-11 h-11 rounded-xl bg-amber-500/15 flex items-center justify-center mb-4">
            <svg class="w-6 h-6 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
          </div>
          <h3 class="text-lg font-semibold text-white">Vérification admin</h3>
          <p class="mt-2 text-sm text-slate-400 leading-relaxed">Contrôlez et validez les présences enregistrées par les hôtesses depuis votre espace organisateur.</p>
        </article>

        <article class="card-hover bg-slate-900/50 border border-slate-800 rounded-2xl p-6">
          <div class="w-11 h-11 rounded-xl bg-rose-500/15 flex items-center justify-center mb-4">
            <svg class="w-6 h-6 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
          </div>
          <h3 class="text-lg font-semibold text-white">Remerciements</h3>
          <p class="mt-2 text-sm text-slate-400 leading-relaxed">Remerciez vos participants après l'événement avec un suivi complet dans la base de données.</p>
        </article>
      </div>
    </div>
  </section>

  <!-- How it works -->
  <section class="py-20 bg-slate-900/30 border-y border-slate-800/60">
    <div class="max-w-6xl mx-auto px-4">
      <div class="text-center max-w-2xl mx-auto mb-14">
        <h2 class="text-3xl sm:text-4xl font-bold text-white">Comment ça marche ?</h2>
        <p class="mt-4 text-slate-400">Trois étapes pour un événement réussi.</p>
      </div>

      <div class="grid md:grid-cols-3 gap-8 relative">
        <div class="hidden md:block absolute top-10 left-[20%] right-[20%] h-px bg-gradient-to-r from-transparent via-emerald-500/30 to-transparent"></div>

        <div class="text-center relative">
          <div class="w-14 h-14 mx-auto rounded-2xl bg-emerald-600 flex items-center justify-center text-xl font-bold text-white shadow-lg shadow-emerald-900/30 mb-5">1</div>
          <h3 class="text-lg font-semibold text-white">Préparez</h3>
          <p class="mt-2 text-sm text-slate-400">Créez l'événement, ajoutez vos invités et assignez vos hôtesses.</p>
        </div>
        <div class="text-center relative">
          <div class="w-14 h-14 mx-auto rounded-2xl bg-emerald-600 flex items-center justify-center text-xl font-bold text-white shadow-lg shadow-emerald-900/30 mb-5">2</div>
          <h3 class="text-lg font-semibold text-white">Accueillez</h3>
          <p class="mt-2 text-sm text-slate-400">Les hôtesses marquent les présences sur le terrain en direct.</p>
        </div>
        <div class="text-center relative">
          <div class="w-14 h-14 mx-auto rounded-2xl bg-emerald-600 flex items-center justify-center text-xl font-bold text-white shadow-lg shadow-emerald-900/30 mb-5">3</div>
          <h3 class="text-lg font-semibold text-white">Confirmez & remerciez</h3>
          <p class="mt-2 text-sm text-slate-400">Validez les présences et envoyez vos remerciements via WhatsApp.</p>
        </div>
      </div>
    </div>
  </section>

  <!-- Roles -->
  <section class="py-20">
    <div class="max-w-6xl mx-auto px-4">
      <div class="grid md:grid-cols-2 gap-6">
        <div class="bg-gradient-to-br from-violet-900/40 to-slate-900 border border-violet-800/40 rounded-2xl p-8">
          <span class="text-xs font-semibold uppercase tracking-wider text-violet-400">Organisateur</span>
          <h3 class="mt-2 text-2xl font-bold text-white">Espace Admin</h3>
          <p class="mt-3 text-slate-400 text-sm leading-relaxed">Pilotez vos événements, supervisez les hôtesses, vérifiez les présences et communiquez avec vos invités.</p>
          <a href="<?= htmlspecialchars($loginUrl) ?>" class="inline-flex mt-6 items-center gap-2 text-sm font-semibold text-violet-300 hover:text-white transition-colors">
            Accéder à l'espace organisateur
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
          </a>
        </div>
        <div class="bg-gradient-to-br from-emerald-900/40 to-slate-900 border border-emerald-800/40 rounded-2xl p-8">
          <span class="text-xs font-semibold uppercase tracking-wider text-emerald-400">Terrain</span>
          <h3 class="mt-2 text-2xl font-bold text-white">Espace Hôtesse</h3>
          <p class="mt-3 text-slate-400 text-sm leading-relaxed">Interface simplifiée pour accueillir les invités, enregistrer leur présence et ajouter de nouveaux participants.</p>
          <a href="<?= htmlspecialchars($loginUrl) ?>" class="inline-flex mt-6 items-center gap-2 text-sm font-semibold text-emerald-300 hover:text-white transition-colors">
            Accéder à l'espace hôtesse
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
          </a>
        </div>
      </div>
    </div>
  </section>

  <!-- CTA -->
  <section class="py-20">
    <div class="max-w-4xl mx-auto px-4">
      <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-emerald-600 to-teal-700 p-10 sm:p-14 text-center shadow-2xl shadow-emerald-900/40">
        <div class="absolute inset-0 bg-[url('data:image/svg+xml,%3Csvg width=\"60\" height=\"60\" viewBox=\"0 0 60 60\" xmlns=\"http://www.w3.org/2000/svg\"%3E%3Cg fill=\"none\" fill-rule=\"evenodd\"%3E%3Cg fill=\"%23ffffff\" fill-opacity=\"0.05\"%3E%3Cpath d=\"M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\"/%3E%3C/g%3E%3C/g%3E%3C/svg%3E')] opacity-50"></div>
        <div class="relative">
          <h2 class="text-3xl sm:text-4xl font-bold text-white">Prêt à organiser votre prochain événement ?</h2>
          <p class="mt-4 text-emerald-100 max-w-xl mx-auto">Rejoignez WELCOMY et offrez à vos invités une expérience d'accueil professionnelle.</p>
          <div class="mt-8 flex flex-col sm:flex-row gap-4 justify-center">
            <a href="<?= htmlspecialchars($registerUrl) ?>" class="inline-flex items-center justify-center px-7 py-3.5 rounded-2xl text-base font-semibold bg-white text-emerald-700 hover:bg-emerald-50 transition-colors">Inscription hôtesse</a>
            <a href="<?= htmlspecialchars($loginUrl) ?>" class="inline-flex items-center justify-center px-7 py-3.5 rounded-2xl text-base font-semibold bg-emerald-800/50 hover:bg-emerald-800/70 text-white border border-emerald-400/30 transition-colors">J'ai déjà un compte</a>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Footer -->
  <footer class="border-t border-slate-800 py-10">
    <div class="max-w-6xl mx-auto px-4 flex flex-col sm:flex-row items-center justify-between gap-4">
      <div class="flex items-center gap-2">
        <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-emerald-500 to-emerald-700 flex items-center justify-center">
          <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/></svg>
        </div>
        <span class="font-semibold text-white">WELCOMY</span>
        <span class="text-slate-600">·</span>
        <span class="text-sm text-slate-500">Propulsé par Asso+</span>
      </div>
      <p class="text-sm text-slate-500 text-center sm:text-right">
        Service client Asso+ :
        <a href="tel:+237654143860" class="text-emerald-400 hover:text-emerald-300 transition-colors">+237 654 143 860</a>
      </p>
    </div>
    <p class="text-center text-xs text-slate-600 mt-6">&copy; <?= date('Y') ?> WELCOMY — Tous droits réservés.</p>
  </footer>

</body>
</html>
