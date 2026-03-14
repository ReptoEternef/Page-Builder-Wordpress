document.addEventListener('DOMContentLoaded', () => {
    const parallaxEls = document.querySelectorAll('.parallax');
    if (!parallaxEls.length) return;

    const defaultSpeed = 0.15;
    const lerpFactor = 0.08;
    let isScrolling = false;

    parallaxEls.forEach(el => {
        el._currentOffset = 0;
        el._targetOffset = 0;
    });

    function updateParallax() {
        let stillMoving = false;

        parallaxEls.forEach(el => {
            const rect = el.getBoundingClientRect();
            const inView = rect.top < window.innerHeight && rect.bottom > 0;

            if (inView) {
                const speed = el.dataset.parallaxSpeed
                    ? parseFloat(el.dataset.parallaxSpeed)
                    : defaultSpeed;

                const progress = 1 - (rect.bottom / (window.innerHeight + rect.height));
                el._targetOffset = (progress - 0.5) * 100 * speed;
            }

            el._currentOffset += (el._targetOffset - el._currentOffset) * lerpFactor;
            el.style.transform = `translateY(${el._currentOffset}px)`;

            // Continue tant que l'écart est visible
            if (Math.abs(el._targetOffset - el._currentOffset) > 0.1) {
                stillMoving = true;
            }
        });

        if (stillMoving) {
            requestAnimationFrame(updateParallax);
        } else {
            isScrolling = false;
        }
    }

    window.addEventListener('scroll', () => {
        if (!isScrolling) {
            isScrolling = true;
            requestAnimationFrame(updateParallax);
        }
    }, { passive: true });
});