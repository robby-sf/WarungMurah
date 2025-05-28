<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Tempat Makan</title>
    @vite('resources/css/app.css')

</head>
<body class="bg-gray-100 text-gray-800">

    <nav class="bg-white shadow-md py-4 px-6">
        <div class="container mx-auto flex justify-between items-center">
            <h1 class="text-2xl font-bold text-blue-600">Rekomendasi Makan A*</h1>
            <a href="/" class="text-blue-500 hover:text-blue-700">Home</a>
        </div>
    </nav>

    <main class="container mx-auto px-4 py-8">
        <h2 class="text-3xl font-semibold text-center mb-8">Daftar Tempat Makan</h2>

        <div class="grid sm:grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach ($warung as $warung)
                <div class="bg-white rounded-2xl shadow p-6 hover:shadow-lg transition">
                    <h3 class="text-xl font-bold text-blue-700">{{ $warung->name }}</h3>
                    <p class="mt-2 text-gray-600 text-sm">📍 Latitude: {{ $warung->latitude }}, Longitude: {{ $warung->longitude }}</p>
                    <p class="text-gray-600 text-sm">⭐ Rating: {{ $warung->rating }}/5</p>
                    <p class="text-gray-600 text-sm">
                        💰 Harga:
                        @if ($warung->price == 1)
                            Murah
                        @elseif ($warung->price == 2)
                            Sedang
                        @else
                            Mahal
                        @endif
                    </p>
                    <p class="text-gray-600 text-sm">🚗 Akses Jalan: {{ $warung->accessibility }}/10</p>
                </div>
            @endforeach
        </div>
    </main>

    <footer class="bg-white shadow-md mt-12">
        <div class="container mx-auto px-4 py-6 text-center text-sm text-gray-500">
            &copy; {{ date('Y') }} Rekomendasi Tempat Makan dengan A* Search
        </div>
    </footer>

</body>
</html>
