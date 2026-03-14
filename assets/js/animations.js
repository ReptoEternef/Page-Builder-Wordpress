document.addEventListener('DOMContentLoaded', function() {
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('is-visible');
                observer.unobserve(entry.target); // se joue une seule fois
            }
        });
    }, {
        threshold: 0.15 // se déclenche quand 15% de l'élément est visible
    });

    document.querySelectorAll('.fade-in, .fade-left, .fade-right, .scale-in, .fade-up-1, .fade-up-2').forEach(el => {
        observer.observe(el);
    });
});