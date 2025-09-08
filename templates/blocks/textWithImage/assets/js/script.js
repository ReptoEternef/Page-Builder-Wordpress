document.addEventListener('DOMContentLoaded', () => {
    const twi_imgs = document.querySelectorAll('.twi-image img');

    twi_imgs.forEach(img => {
        img.addEventListener('click', (e) => {
            showImagePopup(e.target.src);
        });
    });

    function showImagePopup(src) {
        // Vérifier si un overlay existe déjà
        if (document.querySelector('.img-popup-overlay')) return;

        const overlay = document.createElement('div');
        overlay.classList.add('img-popup-overlay');

        const imgPopup = document.createElement('img');
        imgPopup.src = src;

        overlay.appendChild(imgPopup);
        document.body.appendChild(overlay);

        overlay.addEventListener('click', () => {
            overlay.remove();
        });
    }
});
