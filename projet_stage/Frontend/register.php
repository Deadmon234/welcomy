<!DOCTYPE html>
<html lang="fr" class="dark">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>WELCOMY - Enregistrement</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen flex items-center justify-center" style="background: url('https://i.pinimg.com/736x/95/f2/71/95f2713917eb391a9a7cbefe9f1a9d6f.jpg') center/cover fixed;">

  <div class="w-full max-w-md backdrop-blur-lg bg-white/10 border border-white/30 p-6 rounded-2xl shadow-xl mx-4 text-white">
    <!-- <div class="text-center mb-6">
      <img src="https://img.icons8.com/ios-filled/50/ffffff/add-user-group-man-man.png"
           class="mx-auto mb-2 w-12 h-12" />
      <h1 class="text-3xl font-bold text-white mb-1">WELCOMY</h1>
      <p class="text-gray-200">Créer un compte Admin ou Hôtesse</p>
    </div> -->
    <div class="text-center mb-6">
      <img src="https://img.icons8.com/ios-filled/50/6b21a8/add-user-male.png" 
           class="mx-auto mb-2 w-12 h-12" />
      <h1 class="text-3xl font-bold text-purple-700 mb-2">WELCOMY</h1>
      <p class="text-gray- 600">Créer un compte Admin ou Hôtesse</p>
    </div>
    <form method="POST" action="../../Backend/controllers/registerController.php" class="space-y-4">
      <input type="text" name="nom" placeholder="Nom complet"
             class="w-full p-3 rounded-lg bg-white/20 border border-white/30 placeholder-gray-200 focus:ring-2 focus:ring-purple-500 focus:outline-none transition-all" required>

      <input type="email" name="email" placeholder="Adresse email"
             class="w-full p-3 rounded-lg bg-white/20 border border-white/30 placeholder-gray-200 focus:ring-2 focus:ring-purple-500 focus:outline-none transition-all" required>

      <input type="password" name="password" placeholder="Mot de passe"
             class="w-full p-3 rounded-lg bg-white/20 border border-white/30 placeholder-gray-200 focus:ring-2 focus:ring-purple-500 focus:outline-none transition-all" required>

      <!-- <select name="role"
              class="w-full p-3 rounded-lg bg-white/20 border border-white/30 text-gray-200 focus:ring-2 focus:ring-purple-500 focus:outline-none transition-all" required>
        <option value="">-- Sélectionner un rôle --</option>
        <option value="Admin">Admin</option>
        <option value="Hotesse">Hôtesse</option>
      </select> -->

      <select name="role"
        class="w-full p-3 rounded-lg bg-white/20 border border-white/30 text-black focus:ring-2 focus:ring-purple-500 focus:outline-none transition-all" required>
  <option value="">-- Sélectionner un rôle --</option>
  <option value="admin">Admin</option>
  <option value="hotesse">Hôtesse</option>
</select>


      <button type="submit"
              class="w-full bg-purple-700 hover:bg-purple-800 text-white font-semibold p-3 rounded-lg transition-transform hover:scale-[1.02]">
        S'inscrire
      </button>
    </form>
    <p class="mt-4 text-sm text-gray-200 text-center">
      Déjà inscrit ?
      <a href="login.php" class="text-purple-200 underline hover:text-white">Se connecter</a>
    </p>
  </div>

</body>
</html>
