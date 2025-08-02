<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Connexion</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">

  <div class="bg-white p-8 rounded-lg shadow-lg w-full max-w-sm">
    <h2 class="text-2xl font-bold mb-6 text-center text-gray-800">Connexion</h2>

    <form action="{{ route('login.toLogin') }}" method="POST" class="space-y-5">
      @csrf

      <div>
        <label for="email" class="block text-sm font-medium text-gray-700">Adresse e-mail</label>
        <input type="email" name="email" id="email" required
               class="mt-1 block w-full p-3 border border-gray-300 rounded-md shadow-sm focus:ring-orange-500 focus:border-orange-500" 
               placeholder="ex: email@example.com" />
      </div>

      <div>
        <label for="password" class="block text-sm font-medium text-gray-700">Mot de passe</label>
        <input type="password" name="password" id="password" required
               class="mt-1 block w-full p-3 border border-gray-300 rounded-md shadow-sm focus:ring-orange-500 focus:border-orange-500" 
               placeholder="••••••••" />
      </div>

      <button type="submit"
              class="w-full bg-orange-600 text-white py-3 rounded-md font-semibold hover:bg-orange-700 transition">
        Se connecter
      </button>
    </form>
  </div>

</body>
</html>
