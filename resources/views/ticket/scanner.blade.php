<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>QR Code Scanner (Desktop)</title>

  <meta name="csrf-token" content="{{ csrf_token() }}">

  <!-- Tailwind CSS & QR Scanner -->
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
</head>
<body class="bg-gray-100 min-h-screen flex flex-col items-center justify-start  space-y-6">

  <header class="bg-orange-600 text-white p-4 shadow flex justify-between items-center flex-wrap gap-2 w-full">
    <a href="{{ route('ticket.index') }}" 
       class="bg-white text-red-600 px-4 py-2 rounded shadow hover:bg-gray-100 transition text-sm md:text-base">
      Retour
    </a>
  </header>

  <div class="p-4 space-y-6 max-w-4xl mx-auto border flex flex-col items-center bg-white rounded-lg">
    <h1 class="text-2xl sm:text-3xl font-semibold text-gray-800 text-center">
      Scanner le code QR
    </h1>

    <div class="flex flex-col sm:flex-row sm:items-center sm:space-x-4 space-y-4 sm:space-y-0">
      <select id="cameraList"
              class="border border-gray-300 rounded-lg px-4 py-2 w-full sm:w-auto focus:outline-none focus:ring-2 focus:ring-blue-400">
      </select>
    </div>

    <div class="flex justify-center">
      <div id="reader" class="w-64 h-64 sm:w-72 sm:h-72 bg-white rounded-lg shadow-md overflow-hidden"></div>
    </div>
    
  
    
    <button id="result" style="display:none"
            class="bg-green-500 hover:bg-blue-600 text-white font-medium rounded-lg px-5 py-2 w-full sm:w-auto focus:outline-none focus:ring-2 focus:ring-blue-400">
      acces valider
    </button>
    <button id="startBtn"
            class="bg-blue-500 hover:bg-blue-600 text-white font-medium rounded-lg px-5 py-2 w-full sm:w-auto focus:outline-none focus:ring-2 focus:ring-blue-400">
      Démarrer le scan
    </button>
  </div>

  <script>
  const result = document.getElementById('result');
  const select = document.getElementById('cameraList');
  const startBtn = document.getElementById('startBtn');
  const verifyUrl = "{{ route('ticket.verify') }}";
  const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute("content");

  let html5QrCode;
  let isScanning = false; // 🔒 Pour bloquer les multiples lectures

  // Charger les caméras
  Html5Qrcode.getCameras().then(cameras => {
    if (cameras.length === 0) {
      alert("Aucune caméra trouvée.");
      return;
    }
    cameras.forEach(cam => {
      const opt = document.createElement('option');
      opt.value = cam.id;
      opt.text = cam.label || cam.id;
      select.appendChild(opt);
    });
  }).catch(err => {
    alert("Erreur accès caméra : " + err);
  });

  // Démarrer le scan
  startBtn.addEventListener('click', () => {
    const deviceId = select.value;
    if (!deviceId) return alert("Sélectionnez une caméra");

    result.style.display = "none";
    result.classList.remove("bg-green-600", "bg-red-600", "p-5");
    isScanning = false; // Reset le verrou

    html5QrCode = new Html5Qrcode("reader");

    html5QrCode.start(
      { deviceId: { exact: deviceId } },
      { fps: 10, qrbox: 250 },
      decodedText => {
        if (isScanning) return;
        isScanning = true;

        html5QrCode.stop().then(() => {
          // Appel Laravel pour vérifier le code
          fetch(verifyUrl, {
            method: "POST",
            headers: {
              "Content-Type": "application/json",
              "X-CSRF-TOKEN": csrfToken
            },
            body: JSON.stringify({ code: decodedText })
          })
          .then(res => res.json())
          .then(data => {
            result.style.display = "block";
            result.innerHTML = data.valid ? "Accès autorisé" : "Accès refusé";
            result.classList.add(data.valid ? "bg-green-600" : "bg-red-600", "p-5");
          })
          .catch(err => {
            console.error("Erreur vérification :", err);
            alert("Erreur serveur");
          });
        });
      },
      error => {
        console.warn("Échec scan :", error);
      }
    ).catch(err => {
      alert("Impossible de lancer le scanner : " + err);
    });
  });
</script>

</body>
</html>
