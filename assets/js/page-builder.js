// 0. Functions
// 1. Class
// 2. Init
// 3. Add block
// 4. Container block
// 5. Tiny MCE WYSIWYG
// 6. WP import images
// 7. Other options

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

    if (userRole != 'administrator') {
        return;
    }

    singleBlockContainer = blockInstance.DOM;

    // Flèches pour déplacer
    const arrowsContainer = document.createElement('div');
    arrowsContainer.classList.add('arrows-container');

    const upArrow = document.createElement('span');
    upArrow.innerHTML = '⇈';
    upArrow.classList.add('upArrow');
    upArrow.classList.add('prevent-select');
    //upArrow.id = index;
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
    //Creates a backup button in case it couldn't load Iconify
    if (icon.children.length === 0) {
        const backupDeleteBtn = document.createElement('button');
        backupDeleteBtn.innerHTML = 'X';
        icon.appendChild(backupDeleteBtn);
    }
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

        if (!blocksLibrary.hasOwnProperty(this.type) && this.type != 'root') {
            console.log("Le bloc >", this.type, "< ne fait pas parti de la librairie et n'a pas pu être chargé.");
            return;
        }
        
        if (this.type !== 'root') {
            this.html = blocksLibrary[this.type].html;
            this.fields = blocksLibrary[this.type].fields;
            this.displayName = blocksLibrary[this.type].display_name;
            this.wys = [];
            this.addFieldBtns = {};
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
            // IMPORTANT: stop event bubbling to avoid parent containers reacting to child inputs
            e.stopPropagation();

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

            // Checks checkboxes if they're true
            if (fieldEl.type === 'checkbox' && value === true) {
                fieldEl.checked = true;
            }
            
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
let staticFields = ['custom_css', 'color_context', 'blocks','layout', 'height', 'width', 'full-width', "display_desc", "display",
    'object_position', 'object_fit', 'alignment', 'link', 'dimension_unit', 'custom_post_type', 'anchor_id', 'shortcode', 'display_capt'];
let obwpOptions;
let userRole;

const pageRoot = new Block({
    type: 'root',
    id: 'root',
    DOM: {},
    children: []
});


function initPageBuilder() {
    obwpOptions = php.obwp_options;
    pageRoot.DOM = rootContainer;
    userRole = php.user_role;
    
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

    initBlock.values = (!values || Array.isArray(values))
        ? {}
        : structuredClone(values);


    initBlock.setValues();
    addFieldBtn(initBlock);
    
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
    
    // Mettre à jour les valeurs affichées
    pageRoot.apply(block => {
        block.setValues();
        
        // Mettre à jour aussi les dynamic fields
        for (const baseName in block.addFieldBtns) {
            const fieldData = block.addFieldBtns[baseName];
            fieldData.fields.forEach(input => {
                const fullName = input.name;
                const isTrad = !staticFields.includes(fullName);
                
                if (isTrad && block.values[fullName]) {
                    input.value = block.values[fullName][selectedLang] ?? '';
                }
            });
        }
    });
});

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
    //addFieldBtn(block);

    debugBtn.addEventListener('click', () => {
        console.log(block);
    })


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
    // Hide advanced options if the user is not an administrator
    if (userRole != 'administrator') {        
        const advancedOptions = singleBlockContainer.querySelectorAll('.obwp-advanced-options');
        for (const el of advancedOptions) {
            el.style.display = 'none';
        }
    }

    blockInstance.DOM = singleBlockContainer;

    addUIElements(blockInstance);

    parentDOM.appendChild(singleBlockContainer);


    if (blockInstance.type === 'container') {
        blockInstance.setListener();
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
            plugins: 'textcolor link lists',
            toolbar: 'undo redo removeformat | bold italic underline strikethrough link | alignleft aligncenter alignright alignjustify | bullist numlist forecolor',
            textcolor_map: [
                "EEEAE8", "light",
                "2E3F2C", "accented",
                "92C189", "color 2",
                "B1D8A9", "color 2 hover",
                "7A7A7A", "text",
                "5b6077", "light text",
                "1a1e31", "dark text",
            ],
            textcolor_cols: 4,
            min_height: 150
        };

        if (textarea.classList.contains('wysiwyg-h2')) {
            config = {
                target: textarea,
                menubar: true,
                forced_root_block: 'h2',
                plugins: 'textcolor link',
                toolbar: 'undo redo removeformat | bold italic underline strikethrough link | alignleft aligncenter alignright | forecolor',
                textcolor_map: [
                    "EEEAE8", "light",
                    "2E3F2C", "accented",
                    "92C189", "color 2",
                    "B1D8A9", "color 2 hover",
                    "7A7A7A", "text",
                    "5b6077", "light text",
                    "1a1e31", "dark text",
                ],
                textcolor_cols: 4,
                min_height: 60,
                cleanup: true,
                verify_html: true
            };
        }
        else if (textarea.classList.contains('wysiwyg-h3')) {
            config = {
                target: textarea,
                menubar: false,
                forced_root_block: 'h3',
                plugins: 'textcolor link',
                toolbar: 'undo redo removeformat | bold italic underline strikethrough link | alignleft aligncenter alignright | forecolor',
                textcolor_map: [
                    "EEEAE8", "light",
                    "2E3F2C", "accented",
                    "92C189", "color 2",
                    "B1D8A9", "color 2 hover",
                    "7A7A7A", "text",
                    "5b6077", "light text",
                    "1a1e31", "dark text",
                ],
                textcolor_cols: 4,
                min_height: 60
            };
        }

        tinymce.init(config).then(editors => {
            const editor = editors[0];
            if (editor && !editor._listenersAdded) {
                editor._listenersAdded = true;
                editor.on('change', () => setupTinyMCE(editor));
            }
        });
    });
}



function setupTinyMCE(editor) {
    let blockId;
    if (!editor.targetElm.dataset.blockId) {
        blockId = blockInstance.id; // ou ce que tu utilises
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

// WP IMPORT IMAGES
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
            
            // Update preview
            if(imgPreviewContainer.length){
                imgPreviewContainer.empty();
                urls.forEach(url => {
                    $('<img>').attr('src', url).css({ width: '80px', margin: '5px' }).appendTo(imgPreviewContainer);
                });
                imgPreviewContainer.show();
            }
            
            // Find the block using .closest() instead of manual parent navigation
            const blockItem = wpMediaImport.closest('.block-item');
            if (!blockItem.length) {
                console.error('No .block-item found for media import');
                return;
            }
            
            const blockId = blockItem.attr('id');
            if (!blockId) {
                console.error('No ID found on .block-item');
                return;
            }
            
            const block = findObjectByID(blockId);
            if (!block) {
                console.error('Block not found for ID:', blockId);
                return;
            }
            
            const field = wpMediaImport.data('name');
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

        // Remonter jusqu'au .block-item qui contient l'ID
        const blockItem = wpMediaImport.closest('.block-item');
        if (!blockItem.length) {
            console.warn('No .block-item found for', wpMediaImport);
            return;
        }
        
        const blockId = blockItem.attr('id');
        if (!blockId) {
            console.warn('No ID found on .block-item', blockItem);
            return;
        }
        
        const block = findObjectByID(blockId);
        if (!block) {
            console.warn('Block not found for ID:', blockId);
            return;
        }
        
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


//=============================================================================================================================================================
//                                                                        7. OTHER OPTIONS
//=============================================================================================================================================================

// Dynamic Fields
function addFieldBtn(blockInstance) {
    const parentDOM = blockInstance.DOM;
    const addFieldBtns = parentDOM.querySelectorAll('.add-field');
    
    if (!addFieldBtns.length) return;
    
    addFieldBtns.forEach(btn => {
        const fieldBaseName = btn.dataset.fieldName; // ex: "video_link"
        const fieldTrad = btn.dataset.fieldTrad;
        
        // Initialiser la structure si elle n'existe pas
        if (!blockInstance.addFieldBtns[fieldBaseName]) {
            blockInstance.addFieldBtns[fieldBaseName] = {
                btn,
                fields: []
            };
        }
        
        // Recréer les champs existants depuis values
        const existingFields = Object.keys(blockInstance.values).filter(key => 
            key.startsWith(fieldBaseName + '_')
        );
        
        existingFields.forEach(fullFieldName => {
            const indexMatch = fullFieldName.match(/_(\d+)$/);
            if (indexMatch) {
                const index = parseInt(indexMatch[1]);
                createFieldElement(btn, fieldBaseName, index, blockInstance, parentDOM, fieldTrad);
            }
        });
        
        // Gérer l'ajout de nouveaux champs
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            
            // Trouver le prochain index disponible
            const fields = blockInstance.addFieldBtns[fieldBaseName].fields;
            const nextIndex = fields.length;
            
            createFieldElement(btn, fieldBaseName, nextIndex, blockInstance, parentDOM, fieldTrad);
            
            // Forcer la mise à jour du JSON
            const JSON = exportPageJSON();
            refreshSideJSON(JSON);
        });
    });
}

function createFieldElement(btn, fieldBaseName, index, blockInstance, parentDOM, fieldTrad) {
    const fields = blockInstance.addFieldBtns[fieldBaseName].fields;
    const fullFieldName = fieldBaseName + '_' + index;
    
    // Vérifier si le champ existe déjà (éviter les doublons)
    if (fields.some(el => el.name === fullFieldName)) {
        return;
    }
    
    const parent = parentDOM.querySelector('.added-fields');
    if (!parent) {
        console.error('Container .added-fields introuvable pour', blockInstance.type);
        return;
    }
    
    let newEl;
    
    switch (btn.dataset.fieldType) {
        case 'input':
            // Créer un wrapper pour pouvoir ajouter un bouton de suppression
            const wrapper = document.createElement('div');
            wrapper.classList.add('dynamic-field-wrapper');
            wrapper.style.display = 'flex';
            wrapper.style.gap = '0.5rem';
            wrapper.style.alignItems = 'center';
            
            newEl = document.createElement('input');
            newEl.type = 'text';
            newEl.name = fullFieldName;
            newEl.placeholder = btn.dataset.fieldPlaceholder + ' ' + (index + 1);
            
            // Récupérer la valeur existante
            if (fieldTrad === "notrad") {
                newEl.value = blockInstance.values[fullFieldName] ?? '';
            } else {
                const langValue = blockInstance.values[fullFieldName];
                newEl.value = (langValue && langValue[selectedLang]) ? langValue[selectedLang] : '';
            }
            
            // Bouton de suppression
            const deleteBtn = document.createElement('button');
            deleteBtn.type = 'button';
            deleteBtn.innerHTML = '✕';
            deleteBtn.style.cssText = 'padding: 0.45rem 0.75rem; background: #dc3545; color: white; border: none; border-radius: 4px; cursor: pointer;';
            deleteBtn.addEventListener('click', () => {
                // Supprimer du DOM
                wrapper.remove();
                
                // Supprimer du tableau fields
                const fieldIndex = fields.indexOf(newEl);
                if (fieldIndex > -1) {
                    fields.splice(fieldIndex, 1);
                }
                
                // Supprimer des values
                delete blockInstance.values[fullFieldName];
                
                // Mettre à jour le JSON
                const JSON = exportPageJSON();
                refreshSideJSON(JSON);
            });
            
            wrapper.appendChild(newEl);
            wrapper.appendChild(deleteBtn);
            parent.appendChild(wrapper);
            break;
            
        case 'textarea':
            // À implémenter si nécessaire
            break;
            
        default:
            console.warn('Type de champ inconnu:', btn.dataset.fieldType);
            return;
    }
    
    // Ajouter aux champs trackés
    fields.push(newEl);
    
    // Ajouter aux staticFields si notrad
    if (fieldTrad === "notrad" && !staticFields.includes(fullFieldName)) {
        staticFields.push(fullFieldName);
    }
}