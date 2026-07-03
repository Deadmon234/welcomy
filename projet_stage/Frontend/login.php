<!DOCTYPE html>
<html lang="en" class="dark">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>WELCOMY - Sign Up</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen flex items-center justify-center"  style="background: url('https://i.pinimg.com/736x/95/f2/71/95f2713917eb391a9a7cbefe9f1a9d6f.jpg') center/cover fixed;">

  <!-- <div class="w-full max-w-md bg-whrite-500/90 backdrop-whrite-sm p-6 rounded-lg shadow-lg mx-4"> -->
  <div class="w-full max-w-md bg-white/20 backdrop-blur-md border border-white/30 p-6 rounded-2xl shadow-2xl mx-4 text-white">
    <div class="text-center mb-6">
      <img src="https://img.icons8.com/ios-filled/50/6b21a8/add-user-male.png" 
           class="mx-auto mb-2 w-12 h-12" />
      <h1 class="text-3xl font-bold text-purple-700 mb-2">WELCOMY</h1>
      <p class="text-gray- 600">Create your free account</p>
    </div>
    
    <form method="POST" action="../../Backend/controllers/loginController.php" class="space-y-4">
      <input type="email" 
             name="email"
             placeholder="Email Address" 
             class="w-full p-3 rounded-lg border border-gray-200 focus:ring-2 focus:ring-purple-500 focus:outline-none transition-all" 
             required />
      
      <input type="password" 
             name="password"
             placeholder="Password" 
             class="w-full p-3 rounded-lg border border-gray-200 text-black focus:ring-2 focus:ring-purple-500 focus:outline-none transition-all" 
             required />

      <button type="submit" 
              class="w-full bg-purple-700 hover:bg-purple-800 text-white font-semibold p-3 rounded-lg transition-transform hover:scale-[1.02]">
        Sign In
      </button>
    </form>
    <p class="mt-4 text-sm text-gray-200 text-center">
      Pas encore de compte ?
      <a href="register.php" class="text-purple-200 underline hover:text-white">S'inscrire</a>
    </p>
  </div>

</body>
</html>


