<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Rekomendasi Tempat Makan</title>
    @vite(['resources/css/app.css','resources/js/LokasiUser.js', 'resources/js/Map.js'])

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>


</head>


<body class="font-sans leading-relaxed bg-[#0d1117] text-[#f0f6fc]">
    <script>
        window.appData = {
            userLat: {{ $user_lat ?? -7.5576139 }},
            userLng: {{ $user_lng ?? 110.8557427 }},
            warung: @json($warung)
        };
    </script>

    {{-- yyu --}}

    <header class="py-10 text-center">
        <h1 class="text-4xl md:text-5xl font-bold text-[#8b5cf6]">WarungMurah</h1>
        <p class="text-lg mt-2 text-[#9ca3af]">Temukan rekomendasi tempat makan murah terbaik di sekitarmu.</p>
    </header>

    <section class="py-16 text-center bg-[#161b22]">
        <h2 class="text-3xl font-semibold mb-6">Rekomendasi Untukmu</h2>
        <button id="UserLocation" class="px-6 py-3 bg-[#8b5cf6] hover:bg-[#7c3aed] text-white font-medium rounded-lg transition">
            Cari Rekomendasi
        </button>
        <div id="result" class="mt-8">
        </div>
    </section>

    <section class="py-16 px-4 bg-[#0d1117]">
        <h2 class="text-3xl font-semibold text-center mb-6">Peta Lokasi</h2>
        <div class="w-full h-[300px] rounded-xl overflow-hidden shadow-lg" id="map">
        </div>
    </section>

    <footer class="text-center text-sm py-6 mt-10 text-[#9ca3af] border-t border-[#2d333b]">
        &copy; 2025 WarungMurah. Dibuat dengan ❤️ dan Algoritma A*.
    </footer>

</body>
</html>
