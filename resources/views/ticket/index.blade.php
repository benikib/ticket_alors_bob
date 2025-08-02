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


@if ($errors->any() || session('error'))
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const modal = document.getElementById('modal');
        modal?.classList.remove('hidden');
        modal?.classList.add('flex');

        @if (session('error'))
            alert("{{ session('error') }}");
        @endif
    });
</script>
@endif


@if (session('success'))
    <div class="bg-green-100 text-green-700 p-3 rounded mb-4">
        {{ session('success') }}
    </div>
@endif
<body class="bg-gray-100 text-gray-800 font-sans">

  <!-- Header -->
  <header class="bg-orange-600 text-white px-6 py-4 shadow-md flex flex-col md:flex-row md:justify-between md:items-center gap-4">
  <!-- Titre principal -->
  <h1 class="text-2xl font-bold text-center md:text-left">Interface Gestion Billets</h1>

  <!-- Partie droite : utilisateur, logout, bouton scanner -->
  <div class="flex items-center gap-4 flex-wrap justify-center md:justify-end">
    @if(Auth::check())
      <!-- Bonjour + nom -->
      <div class="bg-white text-orange-700 px-4 py-1.5 rounded-xl shadow text-sm font-medium flex items-center gap-2">
        <strong>{{ Auth::user()->role }}</strong>
      </div>

      <!-- Bouton déconnexion -->
      <form method="POST" action="{{ route('login.logout') }}">
        @csrf
        <button type="submit"
                class="bg-white text-red-600 hover:bg-gray-100 transition px-4 py-2 rounded-xl shadow text-sm font-semibold">
          🔓 Se déconnecter
        </button>
      </form>
    @endif

    <!-- Bouton Scanner -->
    <a href="{{ route('billet.scanne') }}" 
       class="flex items-center gap-2 bg-white text-blue-700 hover:bg-gray-100 transition px-4 py-2 rounded-xl shadow text-sm font-semibold">
      <img src="{{ asset('images/qr-scan.png') }}" alt="Scanner" class="w-8 h-8">
      Scanner
    </a>
  </div>
</header>

<!-- Dashboard Statistiques -->
<!-- Détails par type de billet -->

@if(Auth::user()->role !== 'user')
<div class="grid grid-cols-1 gap-4 max-w-5xl mx-auto px-4 mt-6">
  <div class="bg-white p-4 sm:p-6 rounded-lg shadow-lg">
    <h3 class="text-sm sm:text-base font-bold text-gray-700 mb-4 uppercase tracking-wide">Détail par type de billet</h3>

    <div class="overflow-x-auto rounded-lg border border-gray-200">
      <table class="min-w-full divide-y divide-gray-200 text-sm text-left">
        <thead class="bg-gray-100 text-gray-600 uppercase">
          <tr>
            <th class="px-4 py-3">Type</th>
            <th class="px-4 py-3">En ligne</th>
            <th class="px-4 py-3">Guichet</th>
            <th class="px-4 py-3">Montant USD</th>
            <th class="px-4 py-3">Montant CDF</th>
            <th class="px-4 py-3">Total billet(s)</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
          @foreach($billetsParType as $type => $stats)
            <tr class="hover:bg-gray-50">
              <td class="px-4 py-2 font-semibold text-blue-700">{{ $type }}</td>
              <td class="px-4 py-2 text-green-700 font-semibold">{{ $stats['en_ligne'] }} billet(s)</td>
              <td class="px-4 py-2 text-yellow-600 font-semibold">{{ $stats['guichet'] }} billet(s)</td>
              <td class="px-4 py-2 text-green-700 font-semibold">${{ number_format($stats['usd'], 2, ',', ' ') }}</td>
              <td class="px-4 py-2 text-yellow-700 font-semibold">{{ number_format($stats['cdf'], 0, ',', ' ') }} FC</td>
              <td class="px-4 py-2">{{ $stats['total'] }} billet(s)</td>
            </tr>
          @endforeach
        </tbody>
        <tfoot>
          <tr class="bg-gray-100 font-bold">
            <td class="px-4 py-3 text-right" colspan="3">Total général :</td>
            <td class="px-4 py-3 text-green-700">${{ number_format($totalUSD, 2, ',', ' ') }}</td>
            <td class="px-4 py-3 text-yellow-700">{{ number_format($totalCDF, 0, ',', ' ') }} FC</td>
            <td class="px-4 py-3 text-blue-800">{{ $totalBillets }} billet(s)</td>
          </tr>
        </tfoot>
      </table>
    </div>
  </div>
</div>
@endif







  <!-- Contenu principal -->
  <main class="p-4 md:p-6 container mx-auto">
    <div class="flex items-center justify-around gap-4">
      <h2 class="text-xl font-semibold mb-4 text-center md:text-left">Liste des billets</h2>
      @if(Auth::user()->role !== 'user')
      <a class="bg-white open-modal text-red-600 px-4 py-2 mb-3 rounded shadow hover:bg-gray-100 transition text-sm md:text-base">
      <img src="{{ asset('images/enregistrement.png') }}" alt="Scanner" class="w-10 h-10">
      </a>
      @endif
    </div>

    <div class="overflow-x-auto">
        <div class="mb-4">
          <input type="text" id="searchInput" placeholder="Rechercher un billet..."
                class="w-full md:w-1/2 p-3 border  rounded-xl 
                ">
        </div>
        <table class="min-w-full bg-white rounded-xl shadow overflow-hidden">
        <thead class="bg-blue-800 text-white text-sm md:text-base">
          <tr>
            <th class="py-3 px-4 text-left">#</th>
            <th class="py-3 px-4 text-left">Nom</th>
            <th class="py-3 px-4 text-left">Numero Telephone</th>
            <th class="py-3 px-4 text-left">Moyen d'achat</th>
            <th class="py-3 px-4 text-left">Nombre de billet acheté</th>
            <th class="py-3 px-4 text-left">Nombre de billet valide</th>
            <th class="py-3 px-4 text-left">Type de billet</th>

            <th class="py-3 px-4 text-left">Statut</th>
            @if(Auth::user()->role !== 'user')
            <th class="py-3 px-4 text-left">QR Code</th>
            @endif

            
          </tr>
        </thead>
 
        <tbody class="text-sm md:text-base">
          @php $total = 1; @endphp
          @foreach($billets as $billet)
          <tr class="border-b hover:bg-gray-50">
                  <td class="py-2 px-4">{{ $total}}</td>
                  <td class="py-2 px-4">{{ $billet->nom_complet_client }}</td>
                  <td class="py-2 px-4">{{ $billet->numero_client }}</td>
                  <td class="py-2 px-4">{{ $billet->moyen_achat }}</td>
                  <td class="py-2 px-4">{{ $billet->nombre_reel }} billet(s)</td>
                  <td class="py-2 px-4">

                  @if($billet->moyen_achat === 'en_ligne')
                      {{ $billet->occurance_billet }}
                    @else
                      —
                    @endif  
                  </td>
                  <td class="py-2 px-4">{{ $billet->typeBillet->nom_type_billet ?? 'N/A' }}</td>
                  <td class="py-2 px-4">
                    @if($billet->moyen_achat === 'en_ligne')
                      @if($billet->statut_billet === 'valide')
                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-700">Valide</span>
                      @elseif($billet->statut_billet === 'utiliser')
                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-700">Utilisé</span>
                      @else
                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-600">{{ $billet->statut_billet }}</span>
                      @endif
                    @else
                      <span class="text-gray-400">—</span>
                    @endif
                  </td>
                  @if(Auth::user()->role !== 'user')
                  <td class="py-2 px-4">
                    <div class="qr-mini cursor-pointer" data-nom="{{ $billet['nom_complet_client'] }}" data-code="{{ $billet['code_bilet'] }}"></div>
                  </td>
                  @endif
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
      
      <form id="paymentForm" class="space-y-5" method="post" action="{{ route('billet.store') }}">
        @csrf
        <div>
          <label for="fullname" class="block text-sm font-medium text-gray-700">Nom complet</label>
          <input type="text" id="fullname" name="nom_complet_client" required
                 class="mt-1 border block w-full rounded-xl border-gray-300 shadow-sm focus:ring-red-500 focus:border-red-500 p-3" />
        </div>

        <div>
          <label for="email" class="block text-sm font-medium text-gray-700">Téléphone (082...)</label>
          <input type="tel" id="email" name="numero_client" required
                 class="mt-1 block border w-full rounded-xl border-gray-300 shadow-sm focus:ring-red-500 focus:border-red-500 p-3" />
        </div>

        <div>
          <label for="quantity" class="block text-sm font-medium text-gray-700">Nombre de tickets</label>
          <input type="number"  id="quantity" name="nombre_reel" required
                 class="mt-1 border  block w-full rounded-xl border-gray-300 shadow-sm focus:ring-red-500 focus:border-red-500 p-3" />
        </div>

        <div style="display: none;">

          <label for="numero_billet" class="block text-sm font-medium text-gray-700">Numero du billet</label>
          <input type="text" id="numero_billet" name="numero_billet" min="1" value="1" required
                 class="mt-1 border block w-full rounded-xl border-gray-300 shadow-sm focus:ring-red-500 focus:border-red-500 p-3" />
                 @error('numero_billet')
                    <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                @enderror
        </div>

        <div>
          <label for="ticketType" class="block text-sm font-medium text-gray-700">Devise</label>
          <select id="ticketType" name="devise" required
                  class="mt-1 border block w-full rounded-xl border-gray-300 shadow-sm focus:ring-red-500 focus:border-red-500 p-3">
            <option value="usd">USD</option>
            <option value="cdf">CDF</option>
          </select>
        </div>

        <div>
          <label for="ticketType" class="block text-sm font-medium text-gray-700">Type de billet</label>
          <select id="ticketType" name="type_billet" required
                  class="mt-1 border block w-full rounded-xl border-gray-300 shadow-sm focus:ring-red-500 focus:border-red-500 p-3">
            <option value="standard">Standard – 5 000 FC</option>
            <option value="vip">VIP – 10 $</option>
          </select>
        </div>

        <button type="submit"
                class="w-full bg-red-600 hover:bg-red-700 text-white text-lg font-semibold py-3 rounded-xl transition duration-200">
          Effectuer l'enregistrement
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
      <!-- <a id="downloadQr" href="#" download="billet.png"
         class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg inline-block">
        Télécharger
      </a> -->
      <button onclick="telecharger()" class="mt-4 px-4 py-2 bg-blue-600 text-white rounded">Télécharger le code</button>
    </div>
  </div>

  <!-- Script -->
  <!-- Ajoute ce script dans ton HTML -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

<script>

let nomClientQR = "client";
    function telecharger() {
      const canvas = document.querySelector('#qrcode canvas');

      if (!canvas) {
        alert("QR Code non généré !");
        return;
      }

      const imgData = canvas.toDataURL("image/png");
      const { jsPDF } = window.jspdf;
      const pdf = new jsPDF();

      const pageWidth = pdf.internal.pageSize.getWidth();
      const pageHeight = pdf.internal.pageSize.getHeight();
      const qrWidth = 100;
      const x = (pageWidth - qrWidth) / 2;
      const y = 40;

      // Fond coloré (rectangle arrondi)
      pdf.setFillColor(230, 240, 255);
      pdf.roundedRect(x - 10, y - 10, qrWidth + 20, qrWidth + 20, 8, 8, 'F');

      // Titre
      pdf.setFontSize(18);
      pdf.setTextColor(50, 50, 120);
      pdf.text("Bienvenu au spectacle de bob, "+nomClientQR, pageWidth / 2, 25, { align: "center" });

      // QR Code
      pdf.addImage(imgData, "PNG", x, y, qrWidth, qrWidth);

      // Texte sous le QR
      pdf.setFontSize(12);
      pdf.setTextColor(80, 80, 80);
      pdf.text("ALORS BOB", pageWidth / 2, y + qrWidth + 25, { align: "center" });

      pdf.save(`billet-de-${nomClientQR}.pdf`);
    }

    document.addEventListener('DOMContentLoaded', () => {
      const modal = document.getElementById('modal');
      const qrModal = document.getElementById('qrModal');
      const qrCodeDiv = document.getElementById('qrcode');
      const downloadBtn = document.getElementById('downloadQr');
      const closeModal = document.getElementById('closeModal');
      const closeQrBtn = document.getElementById('closeQrModal');

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

          nomClientQR = el.dataset.nom?.replace(/\s+/g, '-').toLowerCase() || "client";

          const qr = new QRCode(qrCodeDiv, {
            text: code,
            width: 200,
            height: 200
          });

          // Attendre que le canvas soit prêt sans setTimeout
          const interval = setInterval(() => {
            const canvas = qrCodeDiv.querySelector('canvas');
            if (canvas) {
              clearInterval(interval);
              const dataUrl = canvas.toDataURL("image/png");
              downloadBtn.href = dataUrl;
              downloadBtn.download = `${code}.png`;
            }
          }, 100);

          qrModal.classList.remove('hidden');
          qrModal.classList.add('flex');
        });
      });

      // Modal Enregistrement
      document.querySelectorAll('.open-modal').forEach(btn => {
        btn.addEventListener('click', () => {
          modal.classList.remove('hidden');
          modal.classList.add('flex');
        });
      });

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
//flitrage
    document.getElementById('searchInput').addEventListener('input', function () {
    const searchValue = this.value.toLowerCase();
    const rows = document.querySelectorAll("tbody tr");

    rows.forEach(row => {
        const nom = row.children[1].textContent.toLowerCase();
        const numero = row.children[2].textContent.toLowerCase();
        const billet = row.children[3].textContent.toLowerCase();
        const type = row.children[5].textContent.toLowerCase();

        const match = nom.includes(searchValue) || numero.includes(searchValue) || billet.includes(searchValue) || type.includes(searchValue);
        row.style.display = match ? '' : 'none';
    });
});
</script>


</body>
</html>
