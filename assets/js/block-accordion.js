/**
 * OBWP Block Accordion System
 * Permet de réduire/développer les blocs pour y voir plus clair
 */

document.addEventListener('DOMContentLoaded', function() {
    initBlockAccordions();
});

function initBlockAccordions() {
    const blockHeaders = document.querySelectorAll('.obwp-block-header');
    
    blockHeaders.forEach(header => {
        // Ajouter l'icône toggle
        const toggleIcon = document.createElement('span');
        toggleIcon.className = 'obwp-accordion-toggle';
        toggleIcon.innerHTML = '▼';
        header.appendChild(toggleIcon);
        
        // Ajouter le click handler
        header.style.cursor = 'pointer';
        header.addEventListener('click', function(e) {
            // Ne pas toggle si on clique sur un input dans le header
            if (e.target.tagName === 'INPUT' || e.target.tagName === 'SELECT') {
                return;
            }
            
            const blockAdmin = header.closest('.obwp-block-admin');
            const blockBody = blockAdmin.querySelector('.obwp-block-body');
            
            // Toggle
            blockAdmin.classList.toggle('obwp-collapsed');
            
            // Animation smooth
            if (blockAdmin.classList.contains('obwp-collapsed')) {
                blockBody.style.maxHeight = blockBody.scrollHeight + 'px';
                requestAnimationFrame(() => {
                    blockBody.style.maxHeight = '0';
                });
            } else {
                blockBody.style.maxHeight = blockBody.scrollHeight + 'px';
                // Retirer le max-height après l'animation
                setTimeout(() => {
                    if (!blockAdmin.classList.contains('obwp-collapsed')) {
                        blockBody.style.maxHeight = 'none';
                    }
                }, 300);
            }
        });
    });
}

// Fonction à appeler quand un nouveau bloc est ajouté
function initAccordionForBlock(blockElement) {
    const header = blockElement.querySelector('.obwp-block-header');
    if (!header || header.querySelector('.obwp-accordion-toggle')) {
        return; // Déjà initialisé
    }
    
    const toggleIcon = document.createElement('span');
    toggleIcon.className = 'obwp-accordion-toggle';
    toggleIcon.innerHTML = '▼';
    header.appendChild(toggleIcon);
    
    header.style.cursor = 'pointer';
    header.addEventListener('click', function(e) {
        if (e.target.tagName === 'INPUT' || e.target.tagName === 'SELECT') {
            return;
        }
        
        const blockAdmin = header.closest('.obwp-block-admin');
        const blockBody = blockAdmin.querySelector('.obwp-block-body');
        
        blockAdmin.classList.toggle('obwp-collapsed');
        
        if (blockAdmin.classList.contains('obwp-collapsed')) {
            blockBody.style.maxHeight = blockBody.scrollHeight + 'px';
            requestAnimationFrame(() => {
                blockBody.style.maxHeight = '0';
            });
        } else {
            blockBody.style.maxHeight = blockBody.scrollHeight + 'px';
            setTimeout(() => {
                if (!blockAdmin.classList.contains('obwp-collapsed')) {
                    blockBody.style.maxHeight = 'none';
                }
            }, 300);
        }
    });
}
