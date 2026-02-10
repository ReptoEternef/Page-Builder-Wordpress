const closeBtn = document.querySelector(".obwp-popup-default .close-popup");
const popupCtn = document.querySelector('.popup-container');

closeBtn.addEventListener('click', () => {
    console.log('click');
    popupCtn.classList.add('hidden');
})