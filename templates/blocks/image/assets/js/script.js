// Script optionnel pour des effets d'interaction
document.addEventListener('DOMContentLoaded', function() {
    const imageLinks = document.querySelectorAll('.block-image .image-link');
    
    imageLinks.forEach(link => {
        // Effet de zoom au clic (lightbox simple)
        link.addEventListener('click', function(e) {
            const img = this.querySelector('img');
            if (img && !this.hasAttribute('href')) {
                e.preventDefault();
                showImageLightbox(img.src, img.alt);
            }
        });
    });
});

function showImageLightbox(src, alt) {
    const overlay = document.createElement('div');
    overlay.className = 'image-lightbox-overlay';
    overlay.innerHTML = `
        <div class="lightbox-content">
            <img src="${src}" alt="${alt}">
            <button class="lightbox-close">&times;</button>
        </div>
    `;
    
    document.body.appendChild(overlay);
    document.body.style.overflow = 'hidden';
    
    // Fermer au clic
    overlay.addEventListener('click', function(e) {
        if (e.target === overlay || e.target.classList.contains('lightbox-close')) {
            overlay.remove();
            document.body.style.overflow = '';
        }
    });
}
