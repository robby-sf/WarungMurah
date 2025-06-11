
let graphNodeLayers = [];
let graphEdgeLayers = [];

function clearGraphLayers(map) {
    graphNodeLayers.forEach(layer => map.removeLayer(layer));
    graphEdgeLayers.forEach(layer => map.removeLayer(layer));
    graphNodeLayers = [];
    graphEdgeLayers = [];
}

function drawAStar(graph,map, astarPath =null){
    clearGraphLayers(map);

    const nodes = graph.nodes;
    const edges = graph.edges;

    if (!nodes || !edges) {
        console.error("Data graf tidak lengkap (nodes atau edges tidak ada).");
        return;
    }

    const pathEdges = new Set();
    if (astarPath && astarPath.length > 1) {
        for (let i = 0; i < astarPath.length - 1; i++) {
            const nodeA = astarPath[i];
            const nodeB = astarPath[i + 1];
            // Kunci dibuat dengan mengurutkan nama node agar konsisten (misal, A-B sama dengan B-A)
            const connectionKey = [nodeA, nodeB].sort().join('-');
            pathEdges.add(connectionKey);
        }
    }

    for (const nodeId in nodes) {
        const node = nodes[nodeId];
        const circle = L.circle([node.lat, node.lng], {
            color: '#c026d3', // Warna ungu tua untuk garis
            fillColor: '#f0abfc', // Warna ungu muda untuk isian
            fillOpacity: 0.9,
            radius: 3 // Radius dalam meter
        }).addTo(map);

        circle.bindPopup(`<b>Node: ${nodeId}</b>`);
        graphNodeLayers.push(circle);
    }

    const drawnConnections = new Set();
     edges.forEach(edge => {
        const fromNode = nodes[edge.from];
        const toNode = nodes[edge.to];
        
        if (fromNode && toNode) {
            const connectionKey = [edge.from, edge.to].sort().join('-');
            
            // Tentukan style berdasarkan apakah edge ini bagian dari rute A*
            let style;
            if (pathEdges.has(connectionKey)) {
                // Style untuk RUTE A*
                style = { color: '#f97316', weight: 6, opacity: 0.9 };
            } else {
                // Style untuk SEMUA JALAN LAIN
                style = { color: '#6b7280', weight: 2, opacity: 0.7, dashArray: '5, 5' };
            }

            const latlngs = [[fromNode.lat, fromNode.lng], [toNode.lat, toNode.lng]];
            const polyline = L.polyline(latlngs, style).addTo(map);
            graphEdgeLayers.push(polyline);
        }
    });


}

function tampilkanRute(lat, lng) {
    if (!window.userLocation) {
        alert("Lokasi pengguna belum tersedia.");
        return;
    }

    if (window.routingControl) {
        map.removeControl(window.routingControl);
    }

    // Buat routing dengan Leaflet Routing Machine + fallback
    window.routingControl = L.Routing.control({
        waypoints: [
            L.latLng(window.userLocation.lat, window.userLocation.lng),
            L.latLng(lat, lng)
        ],
        router: L.Routing.osrmv1({
            serviceUrl: 'https://router.project-osrm.org/route/v1'
        }),
        routeWhileDragging: false,
        createMarker: () => null // opsional: hilangkan marker default
    }).addTo(map)
    .on('routingerror', function(e) {
        console.warn("Gagal routing dari OSRM, fallback ke garis lurus polyline:", e);

        // Buat garis lurus dari lokasi user ke warung
        L.polyline([
            [window.userLocation.lat, window.userLocation.lng],
            [lat, lng]
        ], {
            color: 'orange',
            weight: 4,
            opacity: 0.8,
            dashArray: '5, 10'
        }).addTo(map);
    });
}

function getRentangHarga(skor) {
    switch (skor) {
        case 1:
            return "Rp 10.000 – 20.000";
        case 2:
            return "Rp 20.001 – 30.000";
        case 3:
            return "Rp 30.001 – 50.000";
        default:
            return "Tidak diketahui";
    }
}

  

document.addEventListener("DOMContentLoaded", () =>{
    const SherindemoBtn = document.getElementById("sherin");

    if (!SherindemoBtn) {
        console.error("Tombol tidak ditemukan.");
        return;
    }

    SherindemoBtn.addEventListener("click", () => {
        const latitude = -7.55206205800333;
        const longitude = 110.86513433907469;

        window.map.flyTo([latitude,longitude],18);
        window.userMarker.setLatLng([latitude,longitude]);
        window.userMarker.setPopupContent("<b>Lokasi Anda</b>").openPopup();

        // Panggil kedua endpoint secara bersamaan
            Promise.all([
                    fetch('/sherin').then(res => res.json()), // graph
                    fetch('/get-astar-route')
                    .then(res => {
        console.log("Respons mentah dari /get-astar-route:", res);

        if (!res.ok) {
            throw new Error(`Server error ${res.status} saat mengambil A*`);
        }

        return res.json(); // hanya akan dieksekusi kalau status OK
    })
                ])
                .then(([graphResponse, astarResponse]) => {
                    console.log("=== Hasil dari /sherin ===");
                    console.log("Graph:", graphResponse.graph);

                    console.log("=== Hasil dari /get-astar-route ===");
                    console.log("Path A*:", astarResponse.path);
                    if (astarResponse.status !== 'success' || !astarResponse.path) {
                        console.warn("A* tidak berhasil:", astarResponse.message || "Path kosong");
                        return;
                    }
                    drawAStar(graphResponse.graph, window.map, astarResponse.path);
                })
                .catch(error => {
                    console.error("Error parsing JSON atau server error:", error);
                });




                    })
});

document.addEventListener("DOMContentLoaded", () =>{
    const SherindemoBtn = document.getElementById("lia");

    if (!SherindemoBtn) {
        console.error("Tombol tidak ditemukan.");
        return;
    }

    SherindemoBtn.addEventListener("click", () => {
        const latitude = -7.558067551108021;
        const longitude = 110.85081864147803;

        window.map.flyTo([latitude,longitude],18);
        window.userMarker.setLatLng([latitude,longitude]);
        window.userMarker.setPopupContent("<b>Lokasi Anda</b>").openPopup();

        // Panggil kedua endpoint secara bersamaan
            Promise.all([
                    fetch('/lia').then(res => res.json()), // graph
                    fetch('/get-astar-route2')
                    .then(res => {
        console.log("Respons mentah dari /get-astar-route:", res);

        if (!res.ok) {
            throw new Error(`Server error ${res.status} saat mengambil A*`);
        }

        return res.json(); // hanya akan dieksekusi kalau status OK
    })
                ])
                .then(([graphResponse, astarResponse]) => {
                    console.log("=== Hasil dari /sherin ===");
                    console.log("Graph:", graphResponse.graph);

                    console.log("=== Hasil dari /get-astar-route ===");
                    console.log("Path A*:", astarResponse.path);
                    if (astarResponse.status !== 'success' || !astarResponse.path) {
                        console.warn("A* tidak berhasil:", astarResponse.message || "Path kosong");
                        return;
                    }
                    drawAStar(graphResponse.graph, window.map, astarResponse.path);
                })
                .catch(error => {
                    console.error("Error parsing JSON atau server error:", error);
                });




                    })
});




let routingControl = null;

document.addEventListener("DOMContentLoaded", () => {
    const cariBtn = document.getElementById("cariRekomendasi");

    if (!cariBtn) {
        console.error("Tombol Cari Rekomendasi tidak ditemukan.");
        return;
    }

    cariBtn.addEventListener("click", () => {
        const latitude = -7.558067551108021;
        const longitude = 110.85081864147803;
        console.log("Menggunakan lokasi default:", latitude, longitude);

        window.userLocation = {
            lat: latitude,
            lng: longitude
        };

        // Pindahkan map ke lokasi default
        map.setView([latitude, longitude], 15);

        // Tambahkan marker lokasi default
        if (window.userMarker) map.removeLayer(window.userMarker);
        window.userMarker = L.marker([latitude, longitude])
            .addTo(map)
            .bindPopup("Lokasi Default")
            .openPopup();

        // Kirim lokasi ke server
        const token = document.querySelector('meta[name="csrf-token"]').content;

        fetch('/lokasi', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': token
            },
            body: JSON.stringify({ latitude, longitude })
        })
        .then(res => res.json())
        .then(lokasiData => {
            // Panggil endpoint rekomendasi
            fetch(`/cari?lat=${latitude}&lng=${longitude}`)
                .then(res => res.json())
                .then(rekomendasiData => {
                    tampilkanRekomendasi(rekomendasiData.rekomendasi);
                });
        })
        .catch(err => console.error("Gagal:", err));
    
});



    function tampilkanRekomendasi(data) {
        const container = document.getElementById("cardsContainer");
        container.innerHTML = '';
        if (!data || data.length === 0) {
            container.innerHTML = '<p>Tidak ada rekomendasi ditemukan.</p>';
            return;
        }

        data.forEach(warung => {
            const card = document.createElement("div");
            card.className = "card mb-2";
            card.innerHTML = `
                <button class="group flex flex-col h-full min-h-[260px] w-full rounded-2xl bg-gray-800 border-2 border-transparent shadow-lg transition-all duration-300 hover:bg-[#21262d] hover:ring-2 hover:ring-[#8b5cf6]">
                    <div class="flex flex-col flex-grow p-6">
                        <h5 class="mb-3 text-lg font-semibold text-white line-clamp-2">${warung.name}</h5>

                        <div class="mb-4 flex flex-wrap items-center gap-x-4 gap-y-2 text-sm text-gray-400">
                            <div class="flex items-center gap-2">
                                <svg class="h-4 w-4 text-theme-purple" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 16 16"><path d="..." /></svg>
                                <span>Rating: <strong class="font-bold text-yellow-400">${warung.rating}</strong></span>
                            </div>
                            <div class="flex items-center gap-2">
                                <svg class="h-4 w-4 text-theme-purple" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 16 16"><path d="..." /></svg>
                                <span>Akses: ${warung.accessibility}</span>
                            </div>
                        </div>

                        <div class="mt-auto text-lg text-white">
                            <span>Harga:</span>
                            <strong class="text-lg font-bold text-theme-purple">${getRentangHarga(warung.price)}</strong>
                        </div>
                    </div>
                </button>

            `;
            container.appendChild(card);

            const button = card.querySelector("button");
            button.addEventListener("click", () => {
                tampilkanRute(warung.latitude, warung.longitude);
            });

            // Tambah marker juga
            L.marker([warung.latitude, warung.longitude])
                .addTo(map)
                .bindPopup(`<b>${warung.name}</b><br>Rating: ${warung.rating}/5`);
        });
    }
});


