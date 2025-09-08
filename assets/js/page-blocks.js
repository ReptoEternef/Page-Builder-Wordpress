const addBlockBtn = document.getElementById('add_block_btn');
const debugBtn = document.getElementById('debug_btn');
const blocksContainer = document.getElementById('page_blocks');
const blockTypeSelector = document.getElementById('block-type-selector');
let blocksArray = [];
let allBlocksHTML = {};


// ================================
//            FUNCTIONS
// ================================

function deleteBlock(e) {
    const row = e.target.closest('.block-item'); // ou la classe parent de ton bloc
    const allBlocks = [...document.querySelectorAll('.block-item')];
    const currentIndex = allBlocks.indexOf(row);
    console.log('curr index : ' + currentIndex);

    row.remove();
    blocksArray.splice(currentIndex, 1);
    forceSync();
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

function moveBlock(e) {
    const arrow = e.target;
    const rowId = parseInt(arrow.id, 10);
    const currRow = blocksArray[rowId];
    const upperRow = blocksArray[rowId - 1];
    const lowerRow = blocksArray[rowId + 1];

    //console.log(blocksArray);
    
    flashRow(currRow.DOM);
    
    if (arrow.classList.contains('upArrow') && upperRow) {
        swapElements(currRow.DOM, upperRow.DOM);
        swapArray(blocksArray, rowId, rowId - 1);
    } else if (arrow.classList.contains('downArrow') && lowerRow) {
        swapElements(lowerRow.DOM, currRow.DOM);
        swapArray(blocksArray, rowId + 1, rowId);
    }
    
    console.log(blocksArray);
    // Mettre à jour dataset.order et IDs des flèches
    blocksArray.forEach((block, i) => {
        block.display_order = i;
        const arrows = block.DOM.querySelectorAll('.arrows-container span');
        arrows.forEach(a => a.id = i);
    });

    forceSync();
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

// Insert block in main array
function pushBlocksArray(addedBlock, blockDOM) {
    //console.log(addedBlock);
    addedBlock.display_order = blocksArray.length;
    blocksArray.push(addedBlock);
    // Add DOM to block object
    blocksArray[addedBlock.display_order].DOM = blockDOM;
}



console.log(blocksArray);


// ================================
//  Synchroniser avec input hidden
// ================================

// input a DOM element
function syncBlocksArray(blockDOM, Obj) {
    blockDOM.addEventListener('input', (e) => {
        //console.log(blocksArray[index].values);
        const inputEl = e.target; // l'élément qui a changé
        const field = inputEl.name; // récupère le nom du champ
        const value = inputEl.value;
        index = Obj.display_order;
        console.log('Obj : ' + Obj.display_order);
        console.log('index : ' + index);

        // ---- RESYNC INDEX (if bloc is added, then another bloc deleted, then it desyncs index)
        if (blocksArray[index] === undefined) {
            blocksArray.forEach(block => {
                block.display_order = syncDisplayOrder(block);
                index = block.display_order;
                console.log(block.type + ' : ' + block.display_order);
            });
        }
        console.log(blocksArray);
        console.log('display order : ' + blocksArray[index].display_order);
        // ---------------------------------------------------------------
        
        // on met à jour la valeur dans le bloc correspondant
        if (blocksArray[index].values) {
            //console.log('saved');
            //console.log(blocksArray[index].values[field]);
            blocksArray[index].values[field] = value;
        }
        
        //console.log(blocksArray);

        const data = blocksArray.map(block => {
            //console.log(block);
            return {
                type: block.type,
                display_order: block.display_order,
                values: block.values
            }
        })
  
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
    console.log('JSON');
    //console.log(hiddenInput.value);
    /* console.log(
        JSON.stringify(data, null, 2)
    ); */
}
function syncDisplayOrder(blockObj) {
    return blocksArray.indexOf(blockObj);
}


prettierPage();
function prettierPage() {
    const mainContainer = document.querySelector('#_page_blocks .inside');
    const domBlocks = document.querySelectorAll('.block-item');

    console.log(mainContainer);
    mainContainer.style.setProperty('padding', '0', 'important');
    
    domBlocks.forEach(el => {
        el.classList.add('inside');
        console.log(el);
        el.children[0].classList.add('inner-block');
    });
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
    //console.log(blocksArray);
    
    const html = addedBlock.html;
    
    // Reset inputs values
    addedBlock.values = {};
    addedBlock.fields.forEach(field => {
        addedBlock.values[field] = '';
        //console.log(addedBlock.values);
        //console.log(addedBlock.values[field]);
    });
    
    //addBlockToUI(html, selectedType, index);
    // Temp add block function
    const tempDiv = document.createElement('div');
    tempDiv.innerHTML = html;
    
    
    const newBlock = tempDiv.firstElementChild || document.createElement('div');
    newBlock.classList.add('inside');
    newBlock.classList.add('inner-block');
    
    
    //console.log(newBlock);
    const singleBlockContainer = document.createElement('div');
    singleBlockContainer.classList.add('block-item');
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
    
    //console.log('INIT');
    //console.log(php.pageBlocks);
    php.pageBlocks.forEach((blockInPage, index) => {
        pushBlocksArray(blockInPage, domBlocks[index]);
        syncBlocksArray(domBlocks[index], index);
        
        //console.log(blocksArray[index].values);
        
        addUIElements(domBlocks[index], index);
    });
});



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
            //syncBlocksToInput();
            const blockDiv = trigger.closest('.block-item');
            //console.log(blockDiv[0]);
            //syncBlocksArray(blockDiv,)
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

debugBtn.addEventListener('click', () => {
    console.log(php.pageBlocks);
})






//console.log(php.pageBlocks);