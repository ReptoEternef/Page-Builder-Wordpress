const addBlockBtn = document.getElementById('add_block_btn');
const debugBtn = document.getElementById('debug_btn');
const blocksContainer = document.getElementById('page_blocks');
const pageBlocksJSON = document.getElementById('page_blocks_json');
let JSONtextEl;
const blockTypeSelector = document.getElementById('block-type-selector');
let blocksArray = [];
let allBlocksHTML = {};


// ================================
//            FUNCTIONS
// ================================

function deleteBlock(e) {
    /* const row = e.target.closest('.block-item'); // ou la classe parent de ton bloc
    const allBlocks = [...document.querySelectorAll('.block-item')];
    const currentIndex = allBlocks.indexOf(row); */
    //console.log('curr index : ' + currentIndex);
    const parent = findOutBlockIndex(e.target ,'.block-item');
    const row = parent[0];
    const currentIndex = parent[1];
    console.log(row);

    row.remove();
    blocksArray.splice(currentIndex, 1);
    forceSync();
}

function findOutBlockIndex(element, closestClass) {
    const closest = element.closest(closestClass);
    //console.log(closest[0]);
    const allBlocks = [...document.querySelectorAll('.block-item')];
    //console.log(allBlocks[0]);
    const currentIndex = allBlocks.indexOf(closest);

    return [closest, currentIndex];
}

function flashRow(row) {
    row.classList.add('highlight');
    setTimeout(() => row.classList.remove('highlight'), 300);
}

function addUIElements(singleBlockContainer, index) {
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

    //console.log(singleBlockContainer);
    deleteBtnContainer.addEventListener('click', deleteBlock);
}

/* function moveBlock(e) {
    const arrow = e.target;
    const rowId = parseInt(arrow.id, 10);
    const currRow = blocksArray[rowId];
    const upperRow = blocksArray[rowId - 1];
    const lowerRow = blocksArray[rowId + 1];

    
    flashRow(currRow.DOM);
    
    if (arrow.classList.contains('upArrow') && upperRow) {
        swapElements(currRow.DOM, upperRow.DOM);
        swapArray(blocksArray, rowId, rowId - 1);
    } else if (arrow.classList.contains('downArrow') && lowerRow) {
        swapElements(lowerRow.DOM, currRow.DOM);
        swapArray(blocksArray, rowId + 1, rowId);
    }
    
    blocksArray.forEach((block, i) => {
        block.display_order = i;
        const arrows = block.DOM.querySelectorAll('.arrows-container span');
        arrows.forEach(a => a.id = i);
    });

    forceSync();
} */

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

// Insert block in main array
function pushBlocksArray(addedBlock, blockDOM) {
    //console.log(addedBlock);
    addedBlock.display_order = blocksArray.length;
    blocksArray.push(addedBlock);
    // Add DOM to block object
    blocksArray[addedBlock.display_order].DOM = blockDOM;
}

function setdropdownsName(blockObj) {
    const blockDOM = blockObj.DOM;

    // --- gérer les selects ---
    const selects = blockDOM.querySelectorAll('select');
    if (selects) {
        selects.forEach(dropdown => {
            const field = dropdown.name;
            const selectedValue = blockObj.values[field];

            for (const option of dropdown.options) {
                option.selected = (option.value === selectedValue);
            }
        });
    }

    // --- gérer les checkboxes ---
    const checkboxes = blockDOM.querySelectorAll('input[type="checkbox"]');
    if (checkboxes) {
        checkboxes.forEach(checkbox => {
            const field = checkbox.name;
            const checkedValue = blockObj.values[field];
            checkbox.checked = !!checkedValue; // true ou false
        });
    }
}




// ================================
//  Synchroniser avec input hidden
// ================================

// input a DOM element
function syncBlocksArray(blockDOM, Obj) {
    blockDOM.addEventListener('input', (e) => {
        const inputEl = e.target; // l'élément qui a changé
        const field = inputEl.name; // récupère le nom du champ
        let value;

        // ---- gérer les checkboxes ----
        if (inputEl.type === 'checkbox') {
            value = inputEl.checked; // true ou false
        } else {
            value = inputEl.value;
        }
        // -------------------------------

        index = Obj.display_order;

        // ---- RESYNC INDEX (if bloc is added, then another bloc deleted, then it desyncs index)
        if (blocksArray[index] === undefined) {
            blocksArray.forEach(block => {
                block.display_order = syncDisplayOrder(block);
                index = block.display_order;
            });
        }

        // on met à jour la valeur dans le bloc correspondant
        if (blocksArray[index].values) {
            blocksArray[index].values[field] = value;
        }

        const data = blocksArray.map(block => {
            return {
                type: block.type,
                display_order: block.display_order,
                values: block.values
            }
        });

        dataToJSON(data);
    });
}

function forceSync() {
    
    const data = blocksArray.map(block => {

        return {
            type: block.type,
            display_order: syncDisplayOrder(block),
            values: block.values
        }
    })

    dataToJSON(data);
}
function dataToJSON(data) {
    //console.log('data');
    //console.log(data);
    const hiddenInput = document.getElementById('blocks_data');
    hiddenInput.value = JSON.stringify(data);

    //console.log(pageBlocksJSON);
    refreshSideJSON(data);
    
    /* console.log('JSON');
    console.log(
        JSON.stringify(data, null, 2)
        ); */
    //console.log(hiddenInput.value);
}
function syncDisplayOrder(blockObj) {
    return blocksArray.indexOf(blockObj);
}


prettierPage();
function prettierPage() {
    const mainContainer = document.querySelector('#_page_blocks .inside');
    const domBlocks = document.querySelectorAll('.block-item');

    //console.log(mainContainer);
    mainContainer.style.setProperty('padding', '0', 'important');
    
    domBlocks.forEach(el => {
        el.classList.add('inside');
        //console.log(el);
        el.children[0].classList.add('inner-block');
    });
}

function createSideJSON() {
    const parent = pageBlocksJSON.children[1];
    const tempEl = document.createElement('pre');
    JSONtextEl = tempEl;
    parent.appendChild(JSONtextEl);
    JSONtextEl.id = 'JSON-text-element';
}
function refreshSideJSON(innerText) {
    const parent = pageBlocksJSON.children[1];

    stringifiedJSON = JSON.stringify(innerText, null, 2)
    JSONtextEl.innerHTML = stringifiedJSON;

}




/* document.addEventListener('DOMContentLoaded', () => {
    const hiddenInput = document.getElementById('blocks_data');
}); */

// ================================
//         Ajouter un bloc
// ================================
addBlockBtn.addEventListener('click', () => {
    // Get basic infos
    const selectedType = blockTypeSelector.value;
    const index = blocksArray.length;
    
    // Create instance of added block in array
    const addedBlock = { ...php.blocksLibrary[selectedType] };
    //console.log(php.blocksLibrary);
    
    const html = addedBlock.html;
    
    // Reset inputs values
    addedBlock.values = {};
    addedBlock.fields.forEach(field => {

        addedBlock.values[field] = (field === 'layout') ? 'default' : '';
        //console.log(addedBlock.values);
        //console.log(addedBlock.values[field]);
    });
    //console.log(addedBlock);
    
    //addBlockToUI(html, selectedType, index);
    // Temp add block function
    const tempDiv = document.createElement('div');
    tempDiv.innerHTML = html;
    
    
    const newBlock = tempDiv.firstElementChild || document.createElement('div');
    newBlock.classList.add('inner-block');
    
    
    //console.log(newBlock);
    const singleBlockContainer = document.createElement('div');
    singleBlockContainer.classList.add('block-item');
    singleBlockContainer.classList.add('inside');
    singleBlockContainer.appendChild(newBlock);
    
    const blocksPageContainer = document.querySelector('#_page_blocks .inside');
    blocksPageContainer.appendChild(singleBlockContainer);
    
    pushBlocksArray(addedBlock, singleBlockContainer);
    
    addUIElements(singleBlockContainer, index);
    
    // SYNC CHANGES
    forceSync();
    syncBlocksArray(singleBlockContainer, blocksArray[index]);
});


// ================================
//              INIT
// ================================
document.addEventListener('DOMContentLoaded', () => {
    const domBlocks = document.querySelectorAll('.block-item');
    createSideJSON();
    refreshSideJSON(php.pageBlocks);
    
    //console.log('INIT');
    //console.log(php.pageBlocks);
    php.pageBlocks.forEach((blockInPage, index) => {
        pushBlocksArray(blockInPage, domBlocks[index]);
        syncBlocksArray(domBlocks[index], blockInPage);
        setdropdownsName(blockInPage);
        
        //console.log(blocksArray[index].values);
        
        addUIElements(domBlocks[index], index);
    });
});



// ================================
//   Import d'images wordpress
// ================================
jQuery(document).ready(function($){
    $('body').on('click', '.select-media, .hero-image', function(e){
        e.preventDefault();

        const trigger = $(this);
        const blockField = trigger.closest('.block-field');
        const previewContainer = blockField.find('.preview-container');
        const isGallery = blockField.data('multiple') || false;

        const mediaFrame = wp.media({
            title: 'Choisir une image' + (isGallery ? 's' : ''),
            button: { text: 'Utiliser cette image' + (isGallery ? 's' : '') },
            multiple: isGallery
        });

        mediaFrame.on('select', function(){
            const selection = mediaFrame.state().get('selection').toArray();
            const urls = selection.map(att => att.toJSON().url);

            if(previewContainer.length){
                previewContainer.empty();
                urls.forEach(url => {
                    $('<img>').attr('src', url).css({ width: '80px', margin: '5px' }).appendTo(previewContainer);
                });
                previewContainer.show();
            }

            const parentBlock = findOutBlockIndex(blockField[0], '.block-item');
            const currentIndex = parentBlock[1];
            
            if (blockField[0].dataset.name in blocksArray[currentIndex].values) {
                const value = (urls.length < 2) ? urls[0] : urls;
                const field = blockField[0].dataset.name;
                blocksArray[currentIndex].values[field] = value;
            }

            forceSync();
        });

        mediaFrame.open();
    });
});
// Display preview images at INIT
jQuery(document).ready(function($){
    $('.block-field').each(function(){
        const blockField = $(this);
        const previewContainer = blockField.find('.preview-container');
        const blockIndex = findOutBlockIndex(blockField.closest('.block-item')[0], '.block-item')[1];
        const fieldName = blockField.data('name');

        if (blockIndex !== -1 && fieldName in blocksArray[blockIndex].values) {
            const value = blocksArray[blockIndex].values[fieldName];
            if (!value) return;

            const urls = Array.isArray(value) ? value : [value];

            if(previewContainer.length){
                previewContainer.empty();
                urls.forEach(url => {
                    $('<img>').attr('src', url).css({ width: '80px', margin: '5px' }).appendTo(previewContainer);
                });
                previewContainer.show();
            }
        }
    });
});






/////////////////////////////////
//
//  TINY MCE WYSIWYG
//
/////////////////////////////////

function attachTinyMCEListeners() {
    if (typeof tinymce === 'undefined') return;

    // Écoute tous les éditeurs créés
    tinymce.on('AddEditor', (e) => {
        const editor = e.editor;
        const textarea = document.getElementById(editor.id);
        if (!textarea) return;
        
        const fieldName = textarea.getAttribute('name'); // wys1, wys2...
        const blockEl = textarea.closest('.block-item'); // ton bloc
        if (!blockEl) return;
        
        const blockIndex = findOutBlockIndex(blockEl, '.block-item')[1]; // ou ta logique de display_order
        
        editor.on('keyup change input', () => {
            const content = editor.getContent();
            
            if (blocksArray[blockIndex] && blocksArray[blockIndex].values) {
                blocksArray[blockIndex].values[fieldName] = content;
            }
            
            dataToJSON(blocksArray); // mise à jour du hidden input JSON
            //console.log(blocksArray[blockIndex]);
        });
    });
}

// Appelle la fonction après le rendu de tous les blocs
document.addEventListener('DOMContentLoaded', () => {
    attachTinyMCEListeners();

    // Pour les éditeurs déjà initialisés avant le DOMContentLoaded
    if (typeof tinymce !== 'undefined') {
        Object.keys(tinymce.editors).forEach(id => {
            const editor = tinymce.get(id);
            if (!editor) return;

            const textarea = document.getElementById(id);
            if (!textarea) return;

            const fieldName = textarea.getAttribute('name');
            const blockEl = textarea.closest('.block-item');
            if (!blockEl) return;

            const blockIndex = parseInt(blockEl.dataset.index, 10);

            editor.on('keyup change input', () => {
                const content = editor.getContent();
                if (blocksArray[blockIndex] && blocksArray[blockIndex].values) {
                    blocksArray[blockIndex].values[fieldName] = content;
                }
                dataToJSON(blocksArray);
            });
        });
    }
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

/* debugBtn.addEventListener('click', () => {
    console.log('DEBUG');
    console.log(blocksArray);
}) */