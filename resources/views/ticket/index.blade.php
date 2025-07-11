<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Admin - Gestion des Billets</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
</head>
<body class="bg-gray-100 text-gray-800 font-sans">

  <!-- Header -->
  <header class="bg-orange-600 text-white p-4 shadow flex justify-between items-center flex-wrap gap-2">
    <h1 class="text-xl md:text-2xl font-bold">🎫 Interface Admin – Billets</h1>
    <a href="{{route('ticket.scanne') }}" 
        class="bg-white text-red-600 px-4 py-2 rounded shadow hover:bg-gray-100 transition text-sm md:text-base">
        Scanner un billet
    </a>
  </header>

  <!-- Contenu principal -->
  <main class="p-4 md:p-6 container mx-auto">
    <h2 class="text-xl font-semibold mb-4 text-center md:text-left">Liste des billets</h2>

    <div class="overflow-x-auto">
      <table class="min-w-full bg-white rounded-xl shadow overflow-hidden">
        <thead class="bg-blue-800 text-white text-sm md:text-base">
          <tr>
            <th class="py-3 px-4 text-left"></th>
            <th class="py-3 px-4 text-left">Nom</th>
            <th class="py-3 px-4 text-left">Contact</th>
            <th class="py-3 px-4 text-left">Nombre</th>
            <th class="py-3 px-4 text-left">Type</th>
            <th class="py-3 px-4 text-left">Qr code</th>
          </tr>
        </thead>
        <tbody class="text-sm md:text-base">
          <!-- Exemple statique -->
          @php
            $total = 1;
         @endphp
          @foreach($tickets as $ticket)
            <tr class="border-b hover:bg-gray-50">
                <td class="py-2 px-4">{{ $total }}</td>
                <td class="py-2 px-4">{{ $ticket['nom'] }}</td>
                <td class="py-2 px-4">{{ $ticket['conctat'] }}</td>
                <td class="py-2 px-4">{{ $ticket['n_billet'] }}</td>
                <td class="py-2 px-4">{{ $ticket['vip'] == 1 ? 'VIP' : 'Standard' }}</td>
                <td class="py-2 px-4">{{ $ticket['code'] }}</td>
            </tr>

            @php
                $total = $total+1;
            @endphp
        @endforeach
          <!-- À remplacer par des données dynamiques -->
        </tbody>
      </table>
    </div>
  </main>

</body>
</html>
