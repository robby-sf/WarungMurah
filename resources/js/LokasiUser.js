
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
            radius: 9 // Radius dalam meter
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


// document.addEventListener("DOMContentLoaded", () => {

//     const getLocationBtn = document.getElementById("UserLocation");
//     const result = document.getElementById("result");

//     if (!getLocationBtn) {
//         console.error("Tombol get location tidak ditemukan.");
//         return;
//     }

//     getLocationBtn.addEventListener("click", () => {
//         if (navigator.geolocation) {
//             navigator.geolocation.getCurrentPosition(
//                 function (position) {
//                     // const latitude = position.coords.latitude;
//                     // const longitude = position.coords.longitude;
//                     const latitude = -7.55206205800333;
//                     const longitude = 110.86513433907469;

//                     const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');


//                     fetch('/lokasi', {
//                         method: 'POST',
//                         headers: {
//                         'Content-Type': 'application/json',
//                         'Accept': 'application/json',
//                         'X-CSRF-TOKEN': token
//                         },
//                         body: JSON.stringify({ latitude, longitude })
//                     })
//                         .then(response => response.json())
//                         .then(data => {
//                         // console.log("Respon dari server:", data);

//                         result.innerHTML = `<pre>${JSON.stringify(data, null, 2)}</pre>`;

//                         const userLat = parseFloat(data.user_lat);
//                         const userLng = parseFloat(data.user_lng);
                        

//                         window.appData.userLat = userLat;
//                         window.appData.userLng = userLng;
//                         window.appData.warung = data.warung;

//                         if (window.userMarker) {
//                             window.map.removeLayer(window.userMarker);
//                         }

//                         window.userMarker = L.marker([userLat, userLng])
//                             .addTo(window.map)
//                             .bindPopup(`Lokasi Anda (update)<br>Lat: ${userLat}<br>Lng: ${userLng}`)
//                             .openPopup();

//                         console.log("Pindah ke:", userLat, userLng);


//                         window.map.setView([userLat, userLng], 15); 

//                         if (Array.isArray(data.warung)) {
//                             data.warung.forEach(item => {
//                                 L.marker([item.latitude, item.longitude])
//                                     .addTo(window.map)
//                                     .bindPopup(`<b>${item.name}</b><br>Rating: ${item.rating}/5`);
//                             });

//                         }

//                         fetch('/cari?lat=' + userLat + '&lng=' + userLng)
//     .then(response => response.json())
//     .then(data => {
//         const hasilDiv = document.getElementById('result');

//         if (data.rekomendasi && data.rekomendasi.length > 0) {
//             const topWarung = data.rekomendasi[0];

//             hasilDiv.innerHTML = `
//                 <p><strong>Warung Terbaik:</strong> ${topWarung.name}</p>
//                 <p>Harga: ${topWarung.price}</p>
//                 <p>Rating: ${topWarung.rating}</p>
//                 <p>Aksesibilitas: ${topWarung.accessibility}</p>
//                 <p>Lokasi: (${topWarung.latitude}, ${topWarung.longitude})</p>
//             `;

//             const token = document.querySelector('meta[name="csrf-token"]').content;

//             fetch('/rute', {
//                 method: 'POST',
//                 headers: {
//                     'Content-Type': 'application/json',
//                     'X-CSRF-TOKEN': token
//                 },
//                 body: JSON.stringify({
//                     lat: userLat,
//                     lng: userLng,
//                     goal_id: topWarung.id
//                 })
//             })
//             .then(response => response.json())
//             .then(data => {
//                 if (data.path && data.path.length > 0) {
//                     const nodesMap = {
//                         user: { lat: userLat, lng: userLng },
//                         ...Object.fromEntries(window.appData.warung.map(w => [
//                             w.id,
//                             { lat: w.latitude, lng: w.longitude }
//                         ]))
//                     };

//                     console.log("Path dari A*:", data.path);
//                     console.log("Node Map:", nodesMap);

//                     drawRoute(data.path, nodesMap);
//                 } else {
//                     console.warn("Rute tidak ditemukan:", data.message);
//                 }
//             })
//             .catch(error => {
//                 console.error("Gagal mengambil rute:", error);
//             });

//         } else {
//             hasilDiv.innerHTML = `<p><strong>Tidak ada warung yang ditemukan.</strong></p>`;
//         }
//     })
//     .catch(error => {
//         console.error("Terjadi kesalahan saat mengambil data rekomendasi:", error);
//         const hasilDiv = document.getElementById('result-search');
//         hasilDiv.innerHTML = `<p><strong>Gagal mendapatkan rekomendasi. Silakan coba lagi nanti.</strong></p>`;
//     });



                        
//                     });

                        

//                 },
//                 function (error) {
//                     alert("Gagal mendapatkan lokasi user");
//                 }
//             );
//         } else {
//             alert("Browser tidak mendukung geolocation");
//         }
//     });
// });






