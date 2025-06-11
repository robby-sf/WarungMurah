<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Rekomendasi Tempat Makan</title>
    @vite(['resources/css/app.css','resources/js/LokasiUser.js', 'resources/js/Map.js','resources/js/Rute.js'])

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

    <header class="py-10 text-center">
        <h1 class="text-4xl md:text-5xl font-bold text-[#8b5cf6]">WarungMurah</h1>
        <p class="text-lg mt-2 text-[#9ca3af]">Temukan rekomendasi tempat makan murah terbaik di sekitarmu.</p>
    </header>

    <section class="mb-12">
            <h2 class="text-2xl font-semibold text-center mb-6">Demo Graph</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 max-w-2xl mx-auto">
                <!-- Tombol Lokasi Sherin -->
                <div id="sherin" class="bg-[#161b22] p-6 rounded-lg shadow-lg cursor-pointer transition-all duration-300 hover:bg-[#21262d] hover:ring-2 hover:ring-[#8b5cf6]">
                    <h3 class="text-xl font-bold text-center">Sherin</h3>
                    <p class="text-center text-sm text-gray-400 mt-1">(-7.5520, 110.8651)</p>
                </div>
                <!-- Tombol Lokasi Lia -->
                <div id="lia" class="bg-[#161b22] p-6 rounded-lg shadow-lg cursor-pointer transition-all duration-300 hover:bg-[#21262d] hover:ring-2 hover:ring-[#8b5cf6]">
                    <h3 class="text-xl font-bold text-center">Lia</h3>
                    <p class="text-center text-sm text-gray-400 mt-1">(-7.5580, 110.8645)</p>
                </div>
            </div>
    </section>

    <section class="py-16 text-center bg-[#161b22]">
        <h2 class="text-3xl font-semibold mb-6">Rekomendasi Untukmu</h2>
        <button id="UserLocation" class="px-6 py-3 bg-[#8b5cf6] hover:bg-[#7c3aed] text-white font-medium rounded-lg transition">
            Cari Rekomendasi
        </button>
        <div id="result" class="mt-8">
        </div>
        <div id="result-search" class="mt-8">
        </div>
    </section>

    <section class="py-16 px-4 bg-[#0d1117]">
        <h2 class="text-3xl font-semibold text-center mb-6">Peta Lokasi</h2>
        <div class="w-full h-[600px] rounded-xl overflow-hidden shadow-lg" id="map">
        </div>
    </section>

    <footer class="text-center text-sm py-6 mt-10 text-[#9ca3af] border-t border-[#2d333b]">
        &copy; 2025 WarungMurah. Made with love :<.
    </footer>

</body>
</html>
