console.log('PARENT JS');

// allows anchors to work from other pages than home page
document.addEventListener('DOMContentLoaded', function() {
	const menuLinks = document.querySelectorAll('a[href^="#"], a[href^="/#"], a[href^="https://www.maticadeflor.fr/#"]');
	const isHome = window.location.pathname === '/' || window.location.pathname === '/index.php';

	menuLinks.forEach(link => {
		link.addEventListener('click', function(e) {
		const href = this.getAttribute('href');
		const anchor = href.split('#')[1];
		
		if (!anchor) return;

		if (isHome) {
			// On est sur la home → scroll smooth sans rechargement
			e.preventDefault();
			const target = document.getElementById(anchor);
			if (target) {
			target.scrollIntoView({ behavior: 'smooth' });
			history.replaceState(null, null, '#' + anchor); // met à jour l’URL
			}
		} else {
			// On est sur une autre page → redirection vers la home + ancre
			window.location.href = '/' + '#' + anchor;
		}
		});
	});
});


// MENUS :ACTIVE STYLE

const navLinks = document.querySelectorAll('.menu-principal a');
const sections = document.querySelectorAll('.anchor[id]');

if (sections.length > 0) {
    function updateActive() {
        const triggerPoint = window.innerHeight * 0.4;
        let current = sections[0];

        sections.forEach(section => {
            const rect = section.getBoundingClientRect();
            if (rect.top <= triggerPoint) {
                current = section;
            }
        });

        navLinks.forEach(link => link.classList.remove('active'));
        const active = document.querySelector(`.menu-principal a[href="#${current.id}"]`);
        if (active) active.classList.add('active');
    }

    window.addEventListener('scroll', updateActive);
    updateActive(); // initialisation au chargement
}

window.addEventListener('scroll', updateActive);
window.addEventListener('load', updateActive);


// PHONE MENU

const burgerMenu = document.getElementById("burger-menu");
const burgerBtns = document.querySelectorAll('.burger-btn');
const header = document.querySelector('header');

let isMenuOpen = false;

burgerMenu.addEventListener('click', () => {
	if (!isMenuOpen) {
		isMenuOpen = true;
		header.classList.remove('hidden');
	} else {
		isMenuOpen = false;
		header.classList.add('hidden');
	}

	if (burgerBtns[0].classList.contains('hidden-btn')) {
		burgerBtns[0].classList.remove('hidden-btn');
		burgerBtns[1].classList.add('hidden-btn');
	} else if (burgerBtns[1].classList.contains('hidden-btn')) {
		burgerBtns[1].classList.remove('hidden-btn');
		burgerBtns[0].classList.add('hidden-btn');
	}
})






// PAGE TRANSITIONS
if (php.obwp_options?.page_transitions) {

    // Création de l'overlay
    const overlay = document.createElement('div');
    overlay.id = 'obwp-transition-overlay';
    overlay.innerHTML = `
        <img src="${php.obwp_options.logo_url}" alt="logo" id="obwp-transition-logo">
    `;
    document.body.appendChild(overlay);

    // Fade in à l'arrivée
    requestAnimationFrame(() => {
        overlay.classList.add('fade-out');
    });

    // Intercepte les clics sur les liens internes
    document.addEventListener('click', (e) => {
        const link = e.target.closest('a');
        if (!link) return;

        const href = link.getAttribute('href');
        if (!href) return;

        const isExternal = link.hostname !== window.location.hostname;
        const isAnchor = href.startsWith('#') || href.includes('#') && link.hostname === window.location.hostname && link.pathname === window.location.pathname;
        const isAdmin = href.includes('/wp-admin');
        const opensNewTab = link.target === '_blank';

        if (isExternal || isAnchor || isAdmin || opensNewTab) {
			overlay.classList.add('fade-out');
			return;
		}

		e.preventDefault();
		overlay.classList.remove('fade-out');

		requestAnimationFrame(() => {
			requestAnimationFrame(() => {
				setTimeout(() => {
					window.location.href = href;
				}, 400);
			});
		});
    });
}