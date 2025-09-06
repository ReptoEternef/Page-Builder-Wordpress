const addBlockBtn = document.getElementById('add_block_btn');
const blocksContainer = document.getElementById('page_blocks');
const blockTypeSelector = document.getElementById('block-type-selector');
let blocksArray = [];
let allBlocksHTML = {};


// ================================
//            FUNCTIONS
// ================================

function deleteBlock(e) {
    const btn = e.target;
    const row = btn.parentElement;
    row.remove();

    // Mettre à jour blocksArray après suppression
    blocksArray = Array.from(document.querySelectorAll('#page_blocks .block-item'));

    syncBlocksToInput();
}

function flashRow(row) {
    row.classList.add('highlight');
    setTimeout(() => row.classList.remove('highlight'), 300);
}

function addBlockToUI(block, type, index) {
    // Créer le bloc si block.html existe, sinon un div vide (ex: pour nouveau type)
    const tempDiv = document.createElement('div');
    tempDiv.innerHTML = block || '';

    const newBlock = tempDiv.firstElementChild || document.createElement('div');
    newBlock.classList.add('inputs-container');
    
    // --- Créer l’entrée dans block_json pour le nouveau bloc ---
    if (!genBlocks.block_json[index]) {
        const fields = {};
        // on récupère tous les inputs existants pour initialiser les champs
        newBlock.querySelectorAll('input[name], textarea[name], select[name]').forEach(el => {
            fields[el.name] = el.value || '';
        });
        genBlocks.block_json[index] = {
            type: type,
            order: index,
            fields: fields
        };
    }
    // --- Injection dynamique des valeurs ---
    const data = genBlocks.block_json[index]?.fields || {};
    
    for (let fieldName in data) {
        const value = data[fieldName] || '';
        
        // on cherche l'input ou textarea correspondant
        const input = newBlock.querySelector(`[name="${fieldName}"]`);
        if(input) {
            input.value = value;
        }
        
        // si c'est un champ image, on met à jour aussi la preview
        const imgPreview = newBlock.querySelector(`img[name="${fieldName}"]`);
        if(imgPreview) {
            imgPreview.src = value;
            imgPreview.style.display = value ? 'block' : 'none';
        }
    }
    
    
    // ---------------------------------------
    
    const singleBlockContainer = document.createElement('div');

    singleBlockContainer.classList.add('block-item');
    singleBlockContainer.dataset.order = index;
    singleBlockContainer.dataset.type = type || 'hero'; // par défaut hero

    singleBlockContainer.classList.add('single-block-container');
    singleBlockContainer.classList.add('inside');

    singleBlockContainer.appendChild(newBlock);
    blocksContainer.appendChild(singleBlockContainer);

    blocksArray[index] = singleBlockContainer;

    // Flèches pour déplacer
    const arrowsContainer = document.createElement('div');
    arrowsContainer.classList.add('arrows-container');

    const upArrow = document.createElement('span');
    upArrow.innerHTML = '⇈';
    upArrow.classList.add('upArrow');
    upArrow.classList.add('prevent-select');
    upArrow.id = index;
    upArrow.addEventListener('click', moveBlock);

    const downArrow = document.createElement('span');
    downArrow.innerHTML = '⇊';
    downArrow.classList.add('downArrow');
    downArrow.classList.add('prevent-select');
    downArrow.id = index;
    downArrow.addEventListener('click', moveBlock);

    arrowsContainer.appendChild(upArrow);
    arrowsContainer.appendChild(downArrow);
    singleBlockContainer.insertBefore(arrowsContainer, singleBlockContainer.firstChild);

    // Bouton supprimer
    const deleteBtnContainer = document.createElement('div');
    deleteBtnContainer.classList.add('delete-btn-container');

    // Créer l'icône avec Iconify
    const icon = document.createElement("span");
    icon.classList.add("iconify");
    icon.setAttribute("data-icon", "material-symbols:delete-outline"); // nom de l'icône
    icon.setAttribute("data-width", "20");
    icon.setAttribute("data-height", "20");


    
    deleteBtnContainer.appendChild(icon);
    singleBlockContainer.appendChild(deleteBtnContainer);
    deleteBtnContainer.addEventListener('click', deleteBlock);

    syncBlocksToInput();
}

function moveBlock(e) {
    const arrow = e.target;
    const rowId = parseInt(arrow.id, 10);
    const currRow = blocksArray[rowId];
    const upperRow = blocksArray[rowId - 1];
    const lowerRow = blocksArray[rowId + 1];

    console.log(blocksArray);

    flashRow(currRow);

    if (arrow.classList.contains('upArrow') && upperRow) {
        swapElements(currRow, upperRow);
        swapArray(blocksArray, rowId, rowId - 1);
    } else if (arrow.classList.contains('downArrow') && lowerRow) {
        swapElements(lowerRow, currRow);
        swapArray(blocksArray, rowId + 1, rowId);
    }

    // Mettre à jour dataset.order et IDs des flèches
    blocksArray.forEach((block, i) => {
        block.dataset.order = i;
        const arrows = block.querySelectorAll('.arrows-container span');
        arrows.forEach(a => a.id = i);
    });

    syncBlocksToInput();
}

function swapElements(el1, el2) {
    const parent = el1.parentNode;
    const temp = document.createElement("div");
    parent.insertBefore(temp, el1);
    parent.insertBefore(el1, el2);
    parent.insertBefore(el2, temp);
    parent.removeChild(temp);
}

function swapArray(array, i, j) {
    [array[i], array[j]] = [array[j], array[i]];
}







// ================================
//  Synchroniser avec input hidden
// ================================
function syncBlocksToInput() {
    const hiddenInput = document.getElementById('blocks_data');

    const data = blocksArray.map(block => {
        const fields = {};

        // Pour chaque input, textarea ou select du bloc
        block.querySelectorAll('input[name], textarea[name], select[name]').forEach(el => {
            const name = el.name;

            // Si c’est un champ galerie (data-multiple="true"), convertir la string en tableau
            if(el.dataset.multiple === 'true') {
                // Séparer par virgule et filtrer les valeurs vides
                fields[name] = el.value
                    ? el.value.split(',').map(v => v.trim()).filter(v => v)
                    : [];
            } else {
                fields[name] = el.value;
            }
        });

        return {
            type: block.dataset.type,
            order: parseInt(block.dataset.order, 10),
            fields: fields
        };
    });

    // Stocker le JSON final dans l’input hidden pour que WordPress le sauvegarde
    hiddenInput.value = JSON.stringify(data);
    //console.log(data);
}







document.addEventListener('DOMContentLoaded', () => {
    const hiddenInput = document.getElementById('blocks_data');
});

// ================================
//         Ajouter un bloc
// ================================
addBlockBtn.addEventListener('click', () => {
    console.log('lets add a block');
});


// ================================
//   Générer les blocs au départ
// ================================






// ================================
//   Import d'images wordpress
// ================================
jQuery(document).ready(function($){
    let mediaFrame;

    $('body').on('click', '.select-media, .hero-image', function(e){
        e.preventDefault();

        const trigger = $(this);
        const blockField = trigger.closest('.block-field');
        const input = blockField.find('input[type="text"]');
        const previewContainer = blockField.find('.preview-container');
        const isGallery = blockField.data('multiple') || false;

        if (mediaFrame) {
            mediaFrame.open();
            return;
        }

        mediaFrame = wp.media({
            title: 'Choisir une image' + (isGallery ? 's' : ''),
            button: { text: 'Utiliser cette image' + (isGallery ? 's' : '') },
            multiple: isGallery
        });

        mediaFrame.on('select', function(){
            const selection = mediaFrame.state().get('selection').toArray();
            const urls = selection.map(att => att.toJSON().url);

            // --- Mettre à jour l'input visible ---
            if(isGallery){
                input.val(urls.join(',')); // juste une string séparée par des virgules
            } else {
                input.val(urls[0] || '');
            }

            // --- Affichage des previews ---
            if(previewContainer.length){
                console.log(previewContainer);
                previewContainer.empty();
                urls.forEach(url => {
                    $('<img>').attr('src', url).css({ width: '80px', margin: '5px' }).appendTo(previewContainer);
                });
                previewContainer.show();
            }

            // --- Mettre à jour le hidden input global pour sauvegarde ---
            syncBlocksToInput();
        });

        mediaFrame.open();
    });
});











// DEBUG
/* fetch(ajaxurl, {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: new URLSearchParams({ action: 'get_blocks_html' })
})
.then(res => res.json())
.then(data => 
    console.log(data.data.availableBlocksDebug
)); */
