let gallery = document.querySelectorAll('.gal-image img');
let popup = document.querySelector('.gallery-popup-overlay');
let popupImgContainer = document.querySelectorAll('.gal-img-container');

const displayOverlay = document.querySelector(".display_overlay");
const isDownloadable = document.querySelector(".isDownloadable");


if (displayOverlay.dataset.galOverlay === "1") {
    let currentIndex = 0;

    // --- ouverture du popup sur clic image ---
    gallery.forEach((img, index) => {
        img.addEventListener('click', () => {
            showGalleryPopup(index);
        });
    });

    // --- fermeture si clic en dehors des boutons ---
    popup.addEventListener('click', (e) => {
        if (!e.target.closest('.popup-btn')) {
            popup.style.display = 'none';
        }
    });

    // --- affichage du popup ---
    function showGalleryPopup(index) {
        popup.style.display = 'flex';
        currentIndex = index;

        scrollToCurrent();
        attachDownloadListener();

        // on récupère les boutons APRÈS affichage du popup
        const previousBtn = popup.querySelector('.popup-btn.previous');
        const nextBtn = popup.querySelector('.popup-btn.next');

        // sécurité : si les boutons existent bien
        if (previousBtn && !previousBtn.dataset.listenerAdded) {
            previousBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                currentIndex = (currentIndex - 1 + popupImgContainer.length) % popupImgContainer.length;
                scrollToCurrent();
                attachDownloadListener();
            });
            previousBtn.dataset.listenerAdded = 'true';
        }

        if (nextBtn && !nextBtn.dataset.listenerAdded) {
            nextBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                currentIndex = (currentIndex + 1) % popupImgContainer.length;
                scrollToCurrent();
                attachDownloadListener();
            });
            nextBtn.dataset.listenerAdded = 'true';
        }
    }

    // --- scroll vers l'image courante ---
    function scrollToCurrent() {
        const containerWidth = popup.clientWidth;
        popup.scrollTo({
            left: currentIndex * containerWidth,
            behavior: 'smooth'
        });
    }

    // --- bouton téléchargement ---
    function attachDownloadListener() {
        if (isDownloadable && isDownloadable.dataset.galDownload === "1") {
            const currentContainer = popupImgContainer[currentIndex];
            const downloadBtn = currentContainer.querySelector('.popup-btn.download');

            if (downloadBtn) {
                const newBtn = downloadBtn.cloneNode(true);
                downloadBtn.parentNode.replaceChild(newBtn, downloadBtn);

                newBtn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    const img = currentContainer.querySelector('img');
                    if (img) {
                        const link = document.createElement('a');
                        link.href = img.src;
                        link.download = img.src.split('/').pop();
                        link.click();
                    }
                });
            }
        }
    }

    // --- navigation clavier ---
    document.addEventListener('keydown', (e) => {
        if (popup.style.display === 'flex') {
            if (e.key === 'ArrowLeft') {
                currentIndex = (currentIndex - 1 + popupImgContainer.length) % popupImgContainer.length;
                scrollToCurrent();
                attachDownloadListener();
            } else if (e.key === 'ArrowRight') {
                currentIndex = (currentIndex + 1) % popupImgContainer.length;
                scrollToCurrent();
                attachDownloadListener();
            } else if (e.key === 'Escape') {
                popup.style.display = 'none';
            }
        }
    });
}
