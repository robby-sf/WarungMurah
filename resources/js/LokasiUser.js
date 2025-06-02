
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
                    const latitude = position.coords.latitude;
                    const longitude = position.coords.longitude;

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
                        console.log("Respon dari server:", data);

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
