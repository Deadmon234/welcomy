<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>WELCOMY — Connexion</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <script>
    tailwind.config = {
      theme: {
        extend: {
          fontFamily: { sans: ['Inter', 'system-ui', 'sans-serif'] },
          animation: { 'fade-in': 'fadeIn .4s ease-out' },
          keyframes: { fadeIn: { from: { opacity: 0, transform: 'translateY(12px)' }, to: { opacity: 1, transform: 'translateY(0)' } } }
        }
      }
    };
  </script>
  <style>
    body { font-family: 'Inter', system-ui, sans-serif; }
    .hero-glow {
      background:
        radial-gradient(ellipse 80% 60% at 50% -10%, rgba(16,185,129,.15), transparent),
        radial-gradient(ellipse 50% 40% at 90% 20%, rgba(139,92,246,.1), transparent);
    }
  </style>
</head>
<body class="bg-slate-950 text-slate-100 min-h-screen antialiased hero-glow">

  <div class="min-h-screen flex flex-col">
    <header class="px-4 py-5">
      <div class="max-w-md mx-auto flex items-center justify-between">
        <a href="../../index.php" class="flex items-center gap-2.5 group">
          <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-emerald-500 to-emerald-700 flex items-center justify-center shadow-lg shadow-emerald-900/30 group-hover:scale-105 transition-transform">
            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/></svg>
          </div>
          <span class="text-lg font-bold text-white">WELCOMY</span>
        </a>
        <a href="../../index.php" class="text-sm text-slate-400 hover:text-white transition-colors">Accueil</a>
      </div>
    </header>

    <main class="flex-1 flex items-center justify-center px-4 pb-12">
      <div class="w-full max-w-md animate-fade-in">
        <div class="bg-slate-900/70 border border-slate-800 rounded-2xl shadow-2xl p-8">
          <div class="text-center mb-8">
            <div class="w-14 h-14 mx-auto mb-4 rounded-2xl bg-gradient-to-br from-emerald-500 to-teal-600 flex items-center justify-center shadow-lg shadow-emerald-900/30">
              <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
            </div>
            <h1 class="text-2xl font-bold text-white">Connexion</h1>
            <p class="text-sm text-slate-400 mt-2">Accédez à votre espace organisateur ou hôtesse</p>
          </div>

          <form method="POST" action="../../Backend/controllers/loginController.php" class="space-y-4">
            <div>
              <label for="email" class="block text-xs font-medium text-slate-400 mb-1.5">Adresse email</label>
              <input type="email" id="email" name="email" placeholder="vous@exemple.com"
                     class="w-full bg-slate-800 border border-slate-700 rounded-xl px-4 py-3 text-sm text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all"
                     required>
            </div>
            <div>
              <label for="password" class="block text-xs font-medium text-slate-400 mb-1.5">Mot de passe</label>
              <input type="password" id="password" name="password" placeholder="••••••••"
                     class="w-full bg-slate-800 border border-slate-700 rounded-xl px-4 py-3 text-sm text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all"
                     required>
            </div>
            <button type="submit"
                    class="w-full bg-emerald-600 hover:bg-emerald-500 text-white font-semibold py-3 rounded-xl transition-colors shadow-lg shadow-emerald-900/25 mt-2">
              Se connecter
            </button>
          </form>

          <p class="mt-6 text-sm text-slate-400 text-center">
            Pas encore de compte ?
            <a href="register.php" class="text-emerald-400 hover:text-emerald-300 font-medium transition-colors">Inscription hôtesse</a>
          </p>
        </div>
      </div>
    </main>
  </div>

</body>
</html>
