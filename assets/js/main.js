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
