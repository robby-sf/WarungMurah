document.addEventListener("DOMContentLoaded", () => {
    const getLocationBtn = document.getElementById("UserLocation");
    const result = document.getElementById("result");


    //hapus dibawah kalo udah fix
    if (!getLocationBtn) {
    console.error("Tombol get location tidak ditemukan.");
    return;}


    getLocationBtn.addEventListener("click", () =>{
        if(navigator.geolocation){
            navigator.geolocation.getCurrentPosition(
                function (position){
                    const latitude = position.coords.latitude;
                    const longitude = position.coords.longitude;

                    const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                    fetch('/lokasi',{
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json', //object testing
                            'X-CSRF-TOKEN': token
                        },
                        body: JSON.stringify({ latitude, longitude })
                    })
                    .then(response => response.json())
                    .then(data =>{
                        console.log("Respon dari server:", data); //sementara
                        result.innerHTML = `<pre>${JSON.stringify(data, null, 2)}</pre>`;

                    })
                    .catch(error =>{
                        console.error("Gagal mengirim lokasi ke server:", error);
                    });


                },
                    function(error){
                        alert(" Gagal mendapatkan lokasi user");
                }
            )
            

        }else{
            alert("Sayangnya Browsermu belum mendukung fitur ini")
        }
    

    });
})

