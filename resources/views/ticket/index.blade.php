<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Admin - Gestion des Billets</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
</head>

<body class="bg-gray-100 text-gray-800 font-sans">

  <!-- Header -->
  <header class="bg-orange-600 text-white p-4 shadow flex justify-between items-center flex-wrap gap-2">
    <h1 class="text-xl md:text-2xl font-bold">🎫 Interface Admin – Billets</h1>
    <a href="{{ route('ticket.scanne') }}" 
       class="bg-white text-red-600 px-4 py-2 rounded shadow hover:bg-gray-100 transition text-sm md:text-base">
       Scanner un billet
    </a>
  </header>

  <!-- Contenu principal -->
  <main class="p-4 md:p-6 container mx-auto">
    <div class="flex items-center justify-around gap-4">
      <h2 class="text-xl font-semibold mb-4 text-center md:text-left">Liste des billets</h2>
      <a class="bg-white open-modal text-red-600 px-4 py-2 mb-3 rounded shadow hover:bg-gray-100 transition text-sm md:text-base">
        Enregistrement
      </a>
    </div>

    <div class="overflow-x-auto">
      <table class="min-w-full bg-white rounded-xl shadow overflow-hidden">
        <thead class="bg-blue-800 text-white text-sm md:text-base">
          <tr>
            <th class="py-3 px-4 text-left">#</th>
            <th class="py-3 px-4 text-left">Nom</th>
            <th class="py-3 px-4 text-left">Contact</th>
            <th class="py-3 px-4 text-left">Nombre</th>
            <th class="py-3 px-4 text-left">Type</th>
            <th class="py-3 px-4 text-left">QR Code</th>
            <th class="py-3 px-4 text-left">Statut</th>
            
          </tr>
        </thead>
        <tbody class="text-sm md:text-base">
          @php $total = 1; @endphp
          @foreach($tickets as $ticket)
            <tr class="border-b hover:bg-gray-50">
              <td class="py-2 px-4">{{ $total }}</td>
              <td class="py-2 px-4">{{ $ticket['nom'] }}</td>
              <td class="py-2 px-4">{{ $ticket['conctat'] }}</td>
              <td class="py-2 px-4">{{ $ticket['n_billet'] }} billet(s)</td>
              <td class="py-2 px-4">{{ $ticket['vip'] == 1 ? 'VIP' : 'Standard' }}</td>
              <td class="py-2 px-4">
                <div class="qr-mini cursor-pointer" data-code="{{ $ticket['code'] }}"></div>
              </td>
              <td class="py-2 px-4">{{ $ticket['used'] == 1 ? 'Déjà utilisé' : 'Non scanné' }}</td>
             
            </tr>
            @php $total++; @endphp
          @endforeach
        </tbody>
      </table>
    </div>
  </main>

  <!-- Modal d'achat -->
  <div id="modal" class="fixed inset-0 hidden items-center justify-center bg-black bg-opacity-60 z-50">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg mx-auto p-6 sm:p-8 relative">
      <button id="closeModal" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 text-3xl font-bold">
        &times;
      </button>
      <h2 class="text-2xl sm:text-3xl font-bold text-center text-gray-800 mb-6">Finalisez votre achat</h2>

      <form id="paymentForm" class="space-y-5" method="post" action="{{ route('ticket.store') }}">
        @csrf
        <div>
          <label for="fullname" class="block text-sm font-medium text-gray-700">Nom complet</label>
          <input type="text" id="fullname" name="nom" required
                 class="mt-1 border block w-full rounded-xl border-gray-300 shadow-sm focus:ring-red-500 focus:border-red-500 p-3" />
        </div>

        <div>
          <label for="email" class="block text-sm font-medium text-gray-700">Téléphone (082...)</label>
          <input type="tel" id="email" name="conctat" required
                 class="mt-1 block border w-full rounded-xl border-gray-300 shadow-sm focus:ring-red-500 focus:border-red-500 p-3" />
        </div>

        <div>
          <label for="quantity" class="block text-sm font-medium text-gray-700">Nombre de tickets</label>
          <input type="number" id="quantity" name="n_billet" min="1" value="1" required
                 class="mt-1 border block w-full rounded-xl border-gray-300 shadow-sm focus:ring-red-500 focus:border-red-500 p-3" />
        </div>

        <div>
          <label for="ticketType" class="block text-sm font-medium text-gray-700">Type de billet</label>
          <select id="ticketType" name="vip" required
                  class="mt-1 border block w-full rounded-xl border-gray-300 shadow-sm focus:ring-red-500 focus:border-red-500 p-3">
            <option value="0">Standard – 5 000 FC</option>
            <option value="1">VIP – 10 $</option>
          </select>
        </div>

        <button type="submit"
                class="w-full bg-red-600 hover:bg-red-700 text-white text-lg font-semibold py-3 rounded-xl transition duration-200">
          Procéder au paiement
        </button>
      </form>
    </div>
  </div>

  <!-- Modal QR Code agrandi -->
  <div id="qrModal" class="fixed inset-0 hidden items-center justify-center bg-black bg-opacity-60 z-50">
    <div class="bg-white rounded-2xl flex flex-col items-center justify-center shadow-2xl w-full max-w-md mx-auto p-6 sm:p-8 relative text-center">
      <button id="closeQrModal" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 text-3xl font-bold">
        &times;
      </button>
      <h2 class="text-xl font-bold mb-4">QR Code du billet</h2>
      <div id="qrcode" class="mx-auto mb-4"></div>
      <a id="downloadQr" href="#" download="billet.png"
         class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg inline-block">
        Télécharger
      </a>
    </div>
  </div>

  <!-- Script -->
  <script>
   document.addEventListener('DOMContentLoaded', () => {
  // Déclaration des modals
  const modal = document.getElementById('modal');
  const qrModal = document.getElementById('qrModal');
  const qrCodeDiv = document.getElementById('qrcode');
  const downloadBtn = document.getElementById('downloadQr');
  const closeModal = document.getElementById('closeModal');
  const closeQrBtn = document.getElementById('closeQrModal');

  // === QR mini dans le tableau ===
  document.querySelectorAll('.qr-mini').forEach(el => {
    const code = el.dataset.code;
    new QRCode(el, {
      text: code,
      width: 60,
      height: 60,
      correctLevel: QRCode.CorrectLevel.H
    });

    el.addEventListener('click', () => {
      qrCodeDiv.innerHTML = "";

      const qr = new QRCode(qrCodeDiv, {
        text: code,
        width: 200,
        height: 200
      });

      setTimeout(() => {
        const canvas = qrCodeDiv.querySelector('canvas');
        if (canvas) {
          const dataUrl = canvas.toDataURL("image/png");
          downloadBtn.href = dataUrl;
          downloadBtn.download = `${code}.png`;
        }
      }, 300);

      qrModal.classList.remove('hidden');
      qrModal.classList.add('flex');
    });
  });

  // === Ouverture modal Enregistrement ===
  document.querySelectorAll('.open-modal').forEach(btn => {
    btn.addEventListener('click', () => {
      modal.classList.remove('hidden');
      modal.classList.add('flex');
    });
  });

  // === Fermeture modal Enregistrement ===
  closeModal.onclick = () => {
    modal.classList.add('hidden');
    modal.classList.remove('flex');
  };

  modal.addEventListener('click', e => {
    if (e.target === modal) {
      modal.classList.add('hidden');
      modal.classList.remove('flex');
    }
  });

  // === Fermeture modal QR code ===
  closeQrBtn.onclick = () => {
    qrModal.classList.add('hidden');
    qrModal.classList.remove('flex');
  };

  qrModal.addEventListener('click', e => {
    if (e.target === qrModal) {
      qrModal.classList.add('hidden');
      qrModal.classList.remove('flex');
    }
  });
});

  </script>

</body>
</html>
