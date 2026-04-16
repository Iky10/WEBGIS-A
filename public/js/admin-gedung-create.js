document.addEventListener('DOMContentLoaded', function() {
    const fotoUtamaInput = document.querySelector('#foto_utama');
    if (fotoUtamaInput) {
        fotoUtamaInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(event) {
                    const previewDiv = document.querySelector('#preview-utama');
                    const previewImg = document.querySelector('#img-preview-utama');
                    if (previewDiv && previewImg) {
                        previewImg.src = event.target.result;
                        previewDiv.style.display = 'block';
                    }
                };
                reader.readAsDataURL(file);
            }
        });
    }
});