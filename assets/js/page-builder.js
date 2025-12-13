// 0. Functions
// 1. Class
// 2. Init
// 3. Add block
// 4. Container block
// 5. Tiny MCE WYSIWYG
// 6. WP import images

//=============================================================================================================================================================
//                                                                        0. FUNCTIONS
//=============================================================================================================================================================

// Creates a space to display the side JSON (needs sideJSON = document.getElementById('page_blocks_json'))
function displaySideJSON() {
    const parent = sideJSON.children[1];
    const tempEl = document.createElement('pre');
    JSONtextEl = tempEl;
    parent.appendChild(JSONtextEl);
    JSONtextEl.id = 'JSON-text-element';
}

function refreshSideJSON(innerText) {
    const parent = sideJSON.children[1];

    
    //JSONtextEl.innerHTML = hiddenInput.value;
    stringifiedJSON = JSON.stringify(innerText, null, 2);
    JSONtextEl.innerHTML = innerText;
    //JSONtextEl.innerHTML = stringifiedJSON;
}

function uniqueID(blockType) {
    const id = blockType + '-' + Math.random().toString(16).slice(10)
    
    return id;
}

function serializeBlock(block) {
    return {
        id: block.id,
        type: block.type,
        values: (block.values && !Array.isArray(block.values)) ? block.values : {},
        children: block.children?.map(child => serializeBlock(child)) ?? []
    };
}

function exportPageJSON() {
    const output = pageRoot.children.map(child => serializeBlock(child));
    const json = JSON.stringify(output, null, 2);
    
    hiddenInput.value = json;
    return json;
}

// Generates UI around blocks (arrows and delete)
function addUIElements(blockInstance) {
    singleBlockContainer = blockInstance.DOM;

    // Flèches pour déplacer
    const arrowsContainer = document.createElement('div');
    arrowsContainer.classList.add('arrows-container');

    const upArrow = document.createElement('span');
    upArrow.innerHTML = '⇈';
    upArrow.classList.add('upArrow');
    upArrow.classList.add('prevent-select');
    //upArrow.id = index;
    //console.log(pageRoot.indexOf(singleBlockContainer))
    upArrow.addEventListener('click', () => {
        blockInstance.moveUp();
    });
    
    const downArrow = document.createElement('span');
    downArrow.innerHTML = '⇊';
    downArrow.classList.add('downArrow');
    downArrow.classList.add('prevent-select');
    //downArrow.id = index;
    //downArrow.addEventListener('click', moveBlock);
    downArrow.addEventListener('click', () => {
        blockInstance.moveDown();
    });

    arrowsContainer.appendChild(upArrow);
    arrowsContainer.appendChild(downArrow);
    singleBlockContainer.insertBefore(arrowsContainer, singleBlockContainer.firstChild);

    // Bouton supprimer
    const deleteBtnContainer = document.createElement('div');
    deleteBtnContainer.classList.add('delete-btn-container');
    deleteBtnContainer.addEventListener('click', () => {
        blockInstance.delete();
    });

    // Créer l'icône avec Iconify
    const icon = document.createElement("span");
    icon.classList.add("iconify");
    icon.setAttribute("data-icon", "material-symbols:delete-outline"); // nom de l'icône
    icon.setAttribute("data-width", "20");
    icon.setAttribute("data-height", "20");

    // Delete Btn
    deleteBtnContainer.appendChild(icon);
    singleBlockContainer.appendChild(deleteBtnContainer);
}

// Source - https://stackoverflow.com/a
// Posted by user236139, modified by community. See post 'Timeline' for change history
// Retrieved 2025-12-08, License - CC BY-SA 3.0

function array_move(arr, old_index, new_index) {
    while (old_index < 0) {
        old_index += arr.length;
    }
    while (new_index < 0) {
        new_index += arr.length;
    }
    if (new_index >= arr.length) {
        let k = new_index - arr.length + 1;
        while (k--) {
            arr.push(undefined);
        }
    }
    arr.splice(new_index, 0, arr.splice(old_index, 1)[0]);
    return arr; // for testing purposes
};

function applyToAllBlocks(block, callback) {
    callback(block);
    if (block.children && block.children.length) {
        for (const child of block.children) {
            applyToAllBlocks(child, callback);
        }
    }
}

function findObjectByID(elementID, parentArray) {
    if (!parentArray) {        
        for (const block of pageRoot.children) {
    
            // Si c'est le bon ID → on le retourne
            if (block.id === elementID) {
                return block;
            }
    
            // Si container → recherche récursive
            if (block.type === 'container') {
                const found = findObjectByID(elementID, block);
    
                if (found) {
                    return found; // on sort direct
                }
            }
        }
    } else {
        for (const block of parentArray.children) {
    
            // Si c'est le bon ID → on le retourne
            if (block.id === elementID) {
                return block;
            }
    
            // Si container → recherche récursive
            if (block.type === 'container') {
                const found = findObjectByID(elementID, block);
    
                if (found) {
                    return found; // on sort direct
                }
            }
        }
    }

    // Rien trouvé dans cette branche
    return null;
}





//=============================================================================================================================================================
//                                                                        1. CLASS
//=============================================================================================================================================================

class Block {        
    constructor(raw) {
        this.id = raw.id ?? null;
        this.type = raw.type;
        this.values = (!raw.values || Array.isArray(raw.values))
            ? {}
            : raw.values;
        this.parent = raw.parent ?? null;
        this.children = raw.children ?? [];
        
        if (this.type !== 'root') {
            this.html = blocksLibrary[this.type].html;
            this.fields = blocksLibrary[this.type].fields;
            this.displayName = blocksLibrary[this.type].display_name;
            this.wys = [];
        } else {
            // Block racine → aucune info liée à blocksLibrary
            this.html = null;
            this.fields = [];
            this.displayName = 'ROOT';
        }

        this.DOM = null;
    }

    debug() {
        console.log(blocksLibrary);
    }

    render() {
        // Retourne du HTML, ou met à jour le DOM existant
        return this.html;
    }

    addChild(child, index = null) {
        child.parent = this;

        if (index === null || index >= this.children.length) {
            this.children.push(child);
        } else {
            this.children.splice(index, 0, child);
        }
    }

    setListener() {
        this.DOM.addEventListener('input', (e) => {
            const inputEl = e.target; // l'élément qui a changé
            const field = inputEl.name; // récupère le nom du champ
            let value;

            // ---- gérer les checkboxes ----
            if (inputEl.type === 'checkbox') {
                value = inputEl.checked;
            } else {
                value = inputEl.value;
            }
            // -------------------------------
            const index = this.displayOrder;

            // USELESS ? ---- RESYNC INDEX (if bloc is added, then another bloc deleted, then it desyncs index)
            if (blocksInPage[index] === undefined) {
                blocksInPage.forEach(block => {
                    block.displayOrder = syncDisplayOrder(block);
                    index = block.displayOrder;
                });
            }

            // on met à jour la valeur dans le bloc correspondant
            if (!staticFields.includes(field)) {
                const selectedLang = langSelector.value;

                this.values[field] = this.values[field] || {};
                this.values[field][selectedLang] = value;

            } else {
                this.values[field] = value;
            }

            const JSON = exportPageJSON();
            refreshSideJSON(JSON);
        })
    }

    setValues() {
        for (const fieldName in this.values) {
            const selector = '[name="' + fieldName + '"]';
            const fieldEl = this.DOM.querySelector(selector);
            if (!fieldEl) continue;
            
            const value = staticFields.includes(fieldName)
            ? (this.values[fieldName] ?? '')
            : (this.values[fieldName][selectedLang] ?? '');
            
            // TinyMCE ?
            const editor = tinymce.get(fieldEl.id);
            if (editor) {
                setTinyContentWhenReady(editor, value);
                continue;
            }

            // Sinon field normal
            fieldEl.value = value;
        }
    }


    moveUp() {
        const parent = this.parent;
        if (!parent) return;

        const siblings = parent.children;
        const index = siblings.indexOf(this);
        if (index <= 0) return; // déjà en haut

        // 1. Déplacer dans l’array (LOGIQUE)
        [siblings[index - 1], siblings[index]] = [siblings[index], siblings[index - 1]];

        // 2. Déplacer dans le DOM (VISUEL)
        const dom = this.DOM; // élément HTML du block
        const parentDOM = dom.parentNode;

        const previous = dom.previousElementSibling;
        if (previous) {
            parentDOM.insertBefore(dom, previous);
        }

        const JSON = exportPageJSON();
        refreshSideJSON(JSON);
    }


    moveDown() {
        const parent = this.parent;
        if (!parent) return;
        
        const siblings = parent.children;
        const index = siblings.indexOf(this);
        if (index >= siblings.length - 1) return; // déjà en bas
        
        // 1. Déplacer dans l’array
        [siblings[index + 1], siblings[index]] = [siblings[index], siblings[index + 1]];
        
        // 2. Déplacer dans le DOM
        const dom = this.DOM;
        const next = dom.nextElementSibling;
        
        if (next) {
            dom.parentNode.insertBefore(next, dom);
        }

        const JSON = exportPageJSON();
        refreshSideJSON(JSON);
    }

    delete() {
        const parentArray = this.parent.children
        const index = parentArray.indexOf(this);
        this.DOM.remove();
        parentArray.splice(index, 1);
        const JSON = exportPageJSON();
        refreshSideJSON(JSON);
    }

    apply(callback) {
        callback(this);
        for (const child of this.children) {
            child.apply(callback);
        }
    }
}

//=============================================================================================================================================================
//                                                                        2. INIT
//=============================================================================================================================================================

const blocksLibrary = php.blocksLibrary; // Object
let phpPageBlocks = php.pageBlocks; // Array[Objects]
const blocksInPage = []; // Main Array to store added blocks

let JSONtextEl;
const hiddenInput = document.getElementById('blocks_data');
const sideJSON = document.getElementById('page_blocks_json');
const addBlockBtn = document.getElementById('add_block_btn');
const rootContainer = document.querySelector('#_page_blocks .inside');
const blockTypeSelector = document.getElementById('block-type-selector');
const langSelector = document.getElementById('lang-selector');
let selectedLang = langSelector.selectedOptions[0].value;

const debugBtn = document.getElementById('debug_btn');

// Fields we dont want translation to affect
const staticFields = ['custom_css', 'layout'];
let obwpOptions;

const pageRoot = new Block({
    type: 'root',
    id: 'root',
    DOM: {},
    children: []
});


function initPageBuilder() {
    obwpOptions = php.obwp_options;
    pageRoot.DOM = rootContainer;
    
    displaySideJSON();
    
    phpPageBlocks.forEach((block, index) => {
        initBlocks(block, pageRoot);
    });
    
    const JSON = exportPageJSON();
    refreshSideJSON(JSON);
}

function initBlocks(block, parent) {
    const values = block.values;
    const initBlock = addBlock(parent, block.type);
    initBlock.values = (!values || Array.isArray(values)) ? {} : values;
    initBlock.setValues();
    
    for (const children of block.children) {
        initBlocks(children, initBlock);
    }
}

document.addEventListener('DOMContentLoaded', () => {
    initPageBuilder();
    initTinyFor(pageRoot.DOM);
});


langSelector.addEventListener('change', () => {
    selectedLang = langSelector.selectedOptions[0].value;
    pageRoot.apply(block => block.setValues());
})

//=============================================================================================================================================================
//                                                                        3. ADD BLOCK
//=============================================================================================================================================================

function createBlockInstance(blockType) {
    const base = blocksLibrary[blockType];

    return new Block({
        ...base,
        type: blockType,
        values: {},
        id: uniqueID(blockType)
    });
}

function addBlock(parentBlockArray, blockType, index = null) {
    const block = createBlockInstance(blockType);

    // 1. structure logique
    parentBlockArray.addChild(block, index);

    // 2. structure visuelle
    renderBlock(block);    
    initTinyFor(block.DOM);


    const JSON = exportPageJSON();
    refreshSideJSON(JSON);

    return block;
}

function renderBlock(blockInstance) {
    const parentDOM = blockInstance.parent?.DOM.querySelector('.inner-' + blockInstance.parent.type)
        || blockInstance.parent?.DOM;

    const singleBlockContainer = document.createElement('div');
    singleBlockContainer.classList.add('block-item');
    singleBlockContainer.id = blockInstance.id;

    // Inject HTML
    singleBlockContainer.insertAdjacentHTML('beforeend', blockInstance.html);

    blockInstance.DOM = singleBlockContainer;

    addUIElements(blockInstance);

    parentDOM.appendChild(singleBlockContainer);

    if (blockInstance.type === 'container') {
        containerBlock(blockInstance);
    } else {
        blockInstance.setListener();
    }

    // Set Textareas ready for WYSIWYG and give IDs
    const textareas = singleBlockContainer.querySelectorAll('textarea');
    textareas.forEach(textarea => {
        textarea.id = uniqueID('wys');
        blockInstance.wys.push(textarea);
        textarea.dataset.blockId = blockInstance.id;
    });
}

addBlockBtn.addEventListener('click', () => {
    const selectedBlock = blockTypeSelector.selectedOptions[0].value;
    addBlock(pageRoot, selectedBlock);
});

//=============================================================================================================================================================
//                                                                        4. CONTAINER BLOCK
//=============================================================================================================================================================

function containerBlock(blockInstance) {
    const containerDOM = blockInstance.DOM;
    const containerBlockSelector = containerDOM.querySelector("#block-type-selector");
    const containerAddBtn = containerDOM.querySelector(".container-btn");
    
    containerAddBtn.addEventListener('click', () => {
        const selectedBlock = containerBlockSelector.value;
        addBlock(blockInstance, selectedBlock);
    })
}



//=============================================================================================================================================================
//                                                                        5. TINY MCE WYSIWYG
//=============================================================================================================================================================

function initTinyFor(container) {
    const editors = container.querySelectorAll('textarea.wysiwyg, textarea.wysiwyg-h2, textarea.wysiwyg-h3');

    editors.forEach(textarea => {

        // TinyMCE a besoin d'un id unique, sinon il bug
        if (!textarea.id) {
            textarea.id = uniqueID('wys');
        }

        // Déjà initialisé ? Alors on saute
        if (tinymce.get(textarea.id)) {
            return;
        }

        // Choix de la config en fonction de la classe
        let config = {
            target: textarea,
            menubar: false,
            forced_root_block: 'p',    // défaut pour .wysiwyg
            toolbar: 'undo redo removeformat | bold italic underline strikethrough | alignleft aligncenter alignright alignjustify',
            min_height: 150
        };

        if (textarea.classList.contains('wysiwyg-h2')) {
            config = {
                target: textarea,
                menubar: false,
                forced_root_block: 'h2',
                toolbar: 'undo redo removeformat | bold italic underline strikethrough | alignleft aligncenter alignright',
                min_height: 60
            };
        }
        else if (textarea.classList.contains('wysiwyg-h3')) {
            config = {
                target: textarea,
                menubar: false,
                forced_root_block: 'h3',
                toolbar: 'undo redo removeformat | bold italic underline strikethrough | alignleft aligncenter alignright',
                min_height: 60
            };
        }

        tinymce.init(config).then(editors => {
            const editor = editors[0];
            if (editor && !editor._listenersAdded) {
                editor._listenersAdded = true;
                editor.on('input', () => setupTinyMCE(editor));
            }
        });
    });
}



function setupTinyMCE(editor) {
    let blockId;
    if (!editor.targetElm.dataset.blockId) {
        blockId = blockInstance.id; // ou ce que tu utilises
        console.log(blockId);
    } else {
        blockId = editor.targetElm.dataset.blockId;
    }
    const content = editor.getContent();
    const fieldName = editor.targetElm.name;
    
    // Tu peux retrouver ton block:
    const blockInstance = findObjectByID(blockId, pageRoot);
    
    if (blockInstance && blockInstance.values) {
        const selectedLang = langSelector.value;
        
        blockInstance.values[fieldName] = blockInstance.values[fieldName] ?? {};
        blockInstance.values[fieldName][selectedLang] = content;
    }

    const JSON = exportPageJSON();
    refreshSideJSON(JSON);
}

function setTinyContentWhenReady(editor, value) {
    if (editor.initialized) {
        editor.setContent(value);
        return;
    }

    // Loop jusqu'à ce qu'il soit prêt
    setTimeout(() => setTinyContentWhenReady(editor, value), 30);
}

//=============================================================================================================================================================
//                                                                        6. WP IMPORT IMAGES
//=============================================================================================================================================================

jQuery(document).ready(function($){
    $('body').on('click', '.select-media, .hero-image', function(e){
        e.preventDefault();

        const trigger = $(this);
        const wpMediaImport = trigger.closest('.block-field');
        const imgPreviewContainer = wpMediaImport.find('.preview-container');
        const isGallery = wpMediaImport.data('multiple') || false;

        const mediaFrame = wp.media({
            title: 'Choisir une image' + (isGallery ? 's' : ''),
            button: { text: 'Utiliser cette image' + (isGallery ? 's' : '') },
            multiple: isGallery
        });

        mediaFrame.on('select', function(){
            const selection = mediaFrame.state().get('selection').toArray();
            const urls = selection.map(att => att.toJSON().url);
            
            if(imgPreviewContainer.length){
                imgPreviewContainer.empty();
                urls.forEach(url => {
                    $('<img>').attr('src', url).css({ width: '80px', margin: '5px' }).appendTo(imgPreviewContainer);
                });
                imgPreviewContainer.show();
            }
            
            const innerBlockDOM = wpMediaImport[0].parentElement;
            const blockDOM = innerBlockDOM.parentElement;
            const block = findObjectByID(blockDOM.id)
            
            const field = wpMediaImport[0].dataset.name;
            if (block.fields.includes(field)) {
                const value = (urls.length < 2) ? urls[0] : urls;
                block.values[field] = value;
            }

            const JSON = exportPageJSON();
            refreshSideJSON(JSON);
        });

        mediaFrame.open();
    });
});
// Display preview images at INIT
jQuery(document).ready(function($){
    $('.block-field').each(function(){
        const wpMediaImport = $(this);
        const imgPreviewContainer = wpMediaImport.find('.preview-container');

        const innerBlockDOM = wpMediaImport[0].parentElement;
        const blockDOM = innerBlockDOM.parentElement;
        const block = findObjectByID(blockDOM.id)
        const fieldName = wpMediaImport.data('name');
        
        
        if (block.fields.includes(fieldName)) {
            const value = block.values[fieldName];
            if (!value) return;

            const urls = Array.isArray(value) ? value : [value];

            if(imgPreviewContainer.length){
                imgPreviewContainer.empty();
                urls.forEach(url => {
                    $('<img>').attr('src', url).css({ width: '80px', margin: '5px' }).appendTo(imgPreviewContainer);
                });
                imgPreviewContainer.show();
            }
        }
    });
});