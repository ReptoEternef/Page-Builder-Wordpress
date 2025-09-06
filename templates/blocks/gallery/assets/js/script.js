let img = document.querySelector('.twi-image img');

img.addEventListener('click', (e) => {
    imgDOM = e.target;
    console.log(imgDOM.src);

    showImagePopup(imgDOM.src);
})



function showImagePopup(src) {
  // overlay
  const overlay = document.createElement('div');
  overlay.classList.add('img-popup-overlay');

  // image
  const img = document.createElement('img');
  img.src = src;

  overlay.appendChild(img);
  document.body.appendChild(overlay);

  // fermer au clic
  overlay.addEventListener('click', () => {
    overlay.remove();
  });
}
