/* GALLERY BLOCKS — init pour chaque instance */
document.querySelectorAll('.block-gallery').forEach(blockEl => {

    const displayOverlay = blockEl.querySelector('.display_overlay');
    const isDownloadable = blockEl.querySelector('.isDownloadable');
    const seeMore = blockEl.querySelector('.see_more');

    if (!displayOverlay) return;

    const gallery = blockEl.querySelectorAll('.gal-image img');
    const popup = blockEl.querySelector('.gallery-popup-overlay');
    const popupImgContainer = blockEl.querySelectorAll('.gal-img-container');

    if (displayOverlay.dataset.galOverlay === "1" && popup) {
        let currentIndex = 0;

        gallery.forEach((img, index) => {
            img.addEventListener('click', () => {
                showGalleryPopup(index);
            });
        });

        popup.addEventListener('click', (e) => {
            if (!e.target.closest('.popup-btn')) {
                popup.style.display = 'none';
            }
        });

        function showGalleryPopup(index) {
            popup.style.display = 'flex';
            currentIndex = index;

            scrollToCurrent();
            attachDownloadListener();

            const previousBtn = popup.querySelector('.popup-btn.previous');
            const nextBtn = popup.querySelector('.popup-btn.next');

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

        function scrollToCurrent() {
            const containerWidth = popup.clientWidth;
            popup.scrollTo({
                left: currentIndex * containerWidth,
                behavior: 'smooth'
            });
        }

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

    /* SEE MORE ON PHONE */
    const seeMoreBtn = blockEl.querySelector('.see-more-phone');

    // If See More toggle hasnt been activated we deactivate all that (not very optimised)
    if (seeMore.dataset.galMore != "1") {        
        blockEl.style.maxHeight = 'fit-content';
        blockEl.classList.add('no-fade');
        seeMoreBtn.classList.remove('active');
    }

    if (seeMoreBtn) {
        seeMoreBtn.addEventListener('click', () => {
            blockEl.style.maxHeight = 'fit-content';
            blockEl.classList.add('no-fade');
            seeMoreBtn.classList.remove('active');
        });
    }

});