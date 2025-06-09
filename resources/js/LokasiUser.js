let currentRoutePolyline = null;
function drawRoute(path, nodesMap) {
    if (currentRoutePolyline) {
        map.removeLayer(currentRoutePolyline);
    }

    const latlngs = path.map(id => {
        const node = nodesMap[id];
        return [node.lat, node.lng];
    });

    currentRoutePolyline = L.polyline(latlngs, {
        color: 'red',
        weight: 5
    }).addTo(map);
}



document.addEventListener("DOMContentLoaded", () => {

    const getLocationBtn = document.getElementById("UserLocation");
    const result = document.getElementById("result");

    if (!getLocationBtn) {
        console.error("Tombol get location tidak ditemukan.");
        return;
    }

    getLocationBtn.addEventListener("click", () => {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                function (position) {
                    // const latitude = position.coords.latitude;
                    // const longitude = position.coords.longitude;
                    const latitude = -7.55206205800333;
                    const longitude = 110.86513433907469;

                    const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');


                    fetch('/lokasi', {
                        method: 'POST',
                        headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': token
                        },
                        body: JSON.stringify({ latitude, longitude })
                    })
                        .then(response => response.json())
                        .then(data => {
                        // console.log("Respon dari server:", data);

                        result.innerHTML = `<pre>${JSON.stringify(data, null, 2)}</pre>`;

                        const userLat = parseFloat(data.user_lat);
                        const userLng = parseFloat(data.user_lng);
                        

                        window.appData.userLat = userLat;
                        window.appData.userLng = userLng;
                        window.appData.warung = data.warung;

                        if (window.userMarker) {
                            window.map.removeLayer(window.userMarker);
                        }

                        window.userMarker = L.marker([userLat, userLng])
                            .addTo(window.map)
                            .bindPopup(`Lokasi Anda (update)<br>Lat: ${userLat}<br>Lng: ${userLng}`)
                            .openPopup();

                        console.log("Pindah ke:", userLat, userLng);


                        window.map.setView([userLat, userLng], 15); 

                        if (Array.isArray(data.warung)) {
                            data.warung.forEach(item => {
                                L.marker([item.latitude, item.longitude])
                                    .addTo(window.map)
                                    .bindPopup(`<b>${item.name}</b><br>Rating: ${item.rating}/5`);
                            });

                        }

                        fetch('/cari?lat=' + userLat + '&lng=' + userLng)
    .then(response => response.json())
    .then(data => {
        const hasilDiv = document.getElementById('result');

        if (data.rekomendasi && data.rekomendasi.length > 0) {
            const topWarung = data.rekomendasi[0];

            hasilDiv.innerHTML = `
                <p><strong>Warung Terbaik:</strong> ${topWarung.name}</p>
                <p>Harga: ${topWarung.price}</p>
                <p>Rating: ${topWarung.rating}</p>
                <p>Aksesibilitas: ${topWarung.accessibility}</p>
                <p>Lokasi: (${topWarung.latitude}, ${topWarung.longitude})</p>
            `;

            const token = document.querySelector('meta[name="csrf-token"]').content;

            fetch('/rute', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token
                },
                body: JSON.stringify({
                    lat: userLat,
                    lng: userLng,
                    goal_id: topWarung.id
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.path && data.path.length > 0) {
                    const nodesMap = {
                        user: { lat: userLat, lng: userLng },
                        ...Object.fromEntries(window.appData.warung.map(w => [
                            w.id,
                            { lat: w.latitude, lng: w.longitude }
                        ]))
                    };

                    console.log("Path dari A*:", data.path);
                    console.log("Node Map:", nodesMap);

                    drawRoute(data.path, nodesMap);
                } else {
                    console.warn("Rute tidak ditemukan:", data.message);
                }
            })
            .catch(error => {
                console.error("Gagal mengambil rute:", error);
            });

        } else {
            hasilDiv.innerHTML = `<p><strong>Tidak ada warung yang ditemukan.</strong></p>`;
        }
    })
    .catch(error => {
        console.error("Terjadi kesalahan saat mengambil data rekomendasi:", error);
        const hasilDiv = document.getElementById('result-search');
        hasilDiv.innerHTML = `<p><strong>Gagal mendapatkan rekomendasi. Silakan coba lagi nanti.</strong></p>`;
    });



                        
                    });

                        

                },
                function (error) {
                    alert("Gagal mendapatkan lokasi user");
                }
            );
        } else {
            alert("Browser tidak mendukung geolocation");
        }
    });
});






