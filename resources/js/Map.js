let map;        
let userMarker; 

document.addEventListener("DOMContentLoaded", function () {
    const lat = window.appData.userLat;
    const lng = window.appData.userLng;

    map = L.map('map').setView([lat, lng], 13);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    window.map = map;

    userMarker = L.marker([lat, lng])
        .addTo(map)
        .bindPopup(`Lokasi Anda (${lat})`)
        .openPopup();

    window.userMarker = userMarker;

    window.appData.warung.forEach(item => {
        L.marker([item.latitude, item.longitude])
            .addTo(map)
            .bindPopup(`<b>${item.name}</b><br>Rating: ${item.rating}/5`);
    });


    setTimeout(() => {
        map.invalidateSize();
    }, 500);
});
