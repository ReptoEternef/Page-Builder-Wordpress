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




// -------------------------
// PHONE HAMBURGER MENU + ARIA
// -------------------------
const btn = document.querySelector('.phone-hamburger');
const menu = document.getElementById('phone-menu');

btn.addEventListener('click', () => {
  toggleMenu(menu, btn);
});

// -------------------------
// CLOSES WHEN ANCHOR LINK
// -------------------------
const menuItems = document.querySelectorAll(".menu-item");

menuItems.forEach(item => {
  item.addEventListener('click', () => {
    if (item.classList.contains('menu-item-has-children')) return;
    toggleMenu(menu, btn);
  })
})


// -------------------------
// INTERACT WITH SUBMENUS
// -------------------------
const subMenus = document.querySelectorAll('#phone-menu li .submenu');

subMenus.forEach(subMenu => {
  const parent = subMenu.parentElement;
  parent.classList.add('menu-parent-element');
  const toggleLink = parent.querySelector('a');

  // ajoute une flèche
  parent.children[0].append(document.createTextNode(" ▾"));

  toggleLink.addEventListener('click', (e) => {
    e.preventDefault();

    // Fermer tous les autres sous-menus
    subMenus.forEach(otherMenu => {
      if (otherMenu !== subMenu) {
        otherMenu.style.maxHeight = '0px';
        otherMenu.classList.remove('open-submenu');
      }
    });

    // Toggle du menu cliqué
    if (subMenu.classList.contains('open-submenu')) {
      subMenu.style.maxHeight = '0px';
      subMenu.classList.remove('open-submenu');
    } else {
      subMenu.style.maxHeight = subMenu.scrollHeight + 'px';
      subMenu.classList.add('open-submenu');
    }
  });
});

// -------------------------
//      MENU FUNCTION
// -------------------------
function toggleMenu(menu, btn) {
  const isOpen = menu.classList.contains('open');

  if (isOpen) {
    // Animation de fermeture
    menu.classList.add('closing');
    menu.classList.remove('open');
    btn.setAttribute('aria-expanded', 'false');
    setTimeout(() => {
      menu.classList.remove('closing');
      menu.style.visibility = "hidden";
    }, 300);
  } else {
    // Animation d'ouverture
    menu.style.visibility = "visible";
    menu.classList.add('open');
    btn.setAttribute('aria-expanded', 'true');
  }

  // changement d'icône
  const btnIcon = btn.querySelector('.iconify');
  if (btnIcon) {
    const newIcon = document.createElement('span');
    newIcon.className = 'iconify';
    newIcon.dataset.icon = menu.classList.contains('open')
      ? "bitcoin-icons:cross-filled"
      : "radix-icons:hamburger-menu";
    btnIcon.replaceWith(newIcon);
  }
}