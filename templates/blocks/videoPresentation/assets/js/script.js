const videoFrame = document.querySelector(".vp-video-container iframe");
const thumbnails = document.querySelectorAll(".vp-thumbnails-container .vp-thumbnail")

//console.log(videoFrame);

for (const tn of thumbnails) {
    if (videoFrame.src === tn.dataset.src) {
        setPlaying(tn);
    }

    tn.addEventListener('click', (e) => {
        const tn = e.target;
        setPlaying(tn);

        videoFrame.src = tn.dataset.src;
    })
}

function setPlaying(thumbnail) {
    // reset icon and overlay
    const playingThumbnail = document.querySelector(".vp-playing");
    let oldIcon;
    
    if (playingThumbnail != null) {
        oldIcon = playingThumbnail.querySelector('.iconify');
        playingThumbnail.classList.remove('vp-playing');
    }
    if (oldIcon) {
        oldIcon.remove();
    }

    thumbnail.classList.add('vp-playing');
    
    // Add play btn icon
    const icon = document.createElement('span');
    icon.classList.add('iconify');
    icon.setAttribute('data-icon', 'line-md:play-filled');
    
    thumbnail.appendChild(icon);
}