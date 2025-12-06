// 0. Functions
// 1. Class
// 2. Init
// 3. Add block
// 4. Container block

//=============================================================================================================================================================
//                                                                        0. FUNCTIONS
//=============================================================================================================================================================

// Convert blocksInPage Array to a final JSON for DB
function dataToJSON() {
    const cleanedArray = blocksInPage.map(block => ({
        type: block.type,
        values: block.values,
        id: block.id,
    }));

    //console.log(cleanedArray);
    refreshSideJSON(cleanedArray);
    hiddenInput.value = JSON.stringify(cleanedArray);
    //console.log(hiddenInput.value);
}

// Creates a space to display the side JSON (needs sideJSON = document.getElementById('page_blocks_json'))
function displaySideJSON() {
    const parent = sideJSON.children[1];
    const tempEl = document.createElement('pre');
    JSONtextEl = tempEl;
    parent.appendChild(JSONtextEl);
    JSONtextEl.id = 'JSON-text-element';
}
// Takes a cleaned array such as cleanedArray in dataToJSON()
function refreshSideJSON(innerText) {
    const parent = sideJSON.children[1];

    stringifiedJSON = JSON.stringify(innerText, null, 2);
    JSONtextEl.innerHTML = stringifiedJSON;
}

function uniqueID(blockType) {
    const id = blockType + '-' + Math.random().toString(16).slice(10)
    
    return id;
}

function cleanArray() {
    const cleanedArray = blocksInPage.map(block => {
        return {
            type: block.type,
            values: block.values,
            id: block.id,
        }
    });
}

// Generates UI around blocks (arrows and delete)
function addUIElements(singleBlockContainer, index) {
    // Flèches pour déplacer
    const arrowsContainer = document.createElement('div');
    arrowsContainer.classList.add('arrows-container');

    const upArrow = document.createElement('span');
    upArrow.innerHTML = '⇈';
    upArrow.classList.add('upArrow');
    upArrow.classList.add('prevent-select');
    upArrow.id = index;
    //console.log(pageRoot.indexOf(singleBlockContainer))
    //upArrow.addEventListener('click', moveBlock);

    const downArrow = document.createElement('span');
    downArrow.innerHTML = '⇊';
    downArrow.classList.add('downArrow');
    downArrow.classList.add('prevent-select');
    downArrow.id = index;
    //downArrow.addEventListener('click', moveBlock);

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

    // Delete Btn
    deleteBtnContainer.appendChild(icon);
    singleBlockContainer.appendChild(deleteBtnContainer);

    //console.log(singleBlockContainer);
    //deleteBtnContainer.addEventListener('click', deleteBlock);
}

//=============================================================================================================================================================
//                                                                        1. CLASS
//=============================================================================================================================================================

class Block {        
    constructor(raw) {
        this.id = raw.id ?? null;
        this.type = raw.type;
        this.values = raw.values ?? {};
        this.parent = raw.parent ?? null;
        this.children = raw.children ?? [];
        
        if (this.type !== 'root') {
            this.html = blocksLibrary[this.type].html;
            this.fields = blocksLibrary[this.type].fields;
            this.displayName = blocksLibrary[this.type].display_name;
        } else {
            // Block racine → aucune info liée à blocksLibrary
            this.html = null;
            this.fields = [];
            this.displayName = 'ROOT';
        }

        this.DOM = null;
    }

    test() {
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

    setListener(eventType) {
        this.DOM.addEventListener(eventType, (e) => {
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

            // ---- RESYNC INDEX (if bloc is added, then another bloc deleted, then it desyncs index)
            if (blocksInPage[index] === undefined) {
                blocksInPage.forEach(block => {
                    block.displayOrder = syncDisplayOrder(block);
                    index = block.displayOrder;
                });
            }

            // on met à jour la valeur dans le bloc correspondant
            if (blocksInPage[index].values) {
                let selectedLang = langSelector.selectedOptions[0].value;

                /* if (!blocksInPage[index].values[field]) {
                    blocksInPage[index].values[field] = {};
                }
                blocksInPage[index].values[field][selectedLang] = value; */

                blocksInPage[index].values[field] = value;

                
            }

            /* const data = blocksInPage.map(block => {
                return {
                    type: block.type,
                    displayOrder: block.displayOrder,
                    values: block.values
                }
            }); */

            dataToJSON(blocksInPage);
        })
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

const pageRoot = new Block({
    type: 'root',
    id: 'root',
    children: []
});


function initPageBuilder() {
    //const domBlocks = document.querySelectorAll('.block-item');
    
    displaySideJSON();
    
    phpPageBlocks.forEach((block, index) => {
/*         const blockInstance = new Block(block);
        blockInstance.DOM = domBlocks[index];
        blockInstance.pushInArray(blockInstance);

        blockInstance.setListener('input'); */
        //addBlockToUI(rootContainer, block, index);
    });

    dataToJSON(blocksInPage);
}

document.addEventListener('DOMContentLoaded', initPageBuilder);

//=============================================================================================================================================================
//                                                                        3. ADD BLOCK
//=============================================================================================================================================================
// Asks for an element, the block type (string), and where to place the block
function addBlockToUI(parentDOM, blockType, index = 0) {
    // inst new block and give unique ID
    const base = blocksLibrary[blockType];
    const blockInstance = new Block({
        ...base,
        type: blockType,
        values: {},
        id: uniqueID(blockType)
    });
    
    // Prepare div to insert UI elements
    const singleBlockContainer = document.createElement('div');
    singleBlockContainer.classList.add('block-item');
    singleBlockContainer.id = blockInstance.id;
    singleBlockContainer.insertAdjacentHTML('beforeend', blockInstance.html);

    // Add arrows and delete btn
    addUIElements(singleBlockContainer, index);

    // Add block to the DOM
    blockInstance.DOM = parentDOM.appendChild(singleBlockContainer);
    //blockInstance.setListener('input');
    //dataToJSON(blocksInPage);
    
    if (blockType === 'container') {
        containerBlock(parentDOM, blockInstance);
    }

    return blockInstance;
}

addBlockBtn.addEventListener('click', () => {
    const selectedBlock = blockTypeSelector.selectedOptions[0].value;
    const index = blocksInPage.length;

    const newBlock = addBlockToUI(rootContainer, selectedBlock);
    pageRoot.addChild(newBlock);
    console.log(pageRoot);
    //addBlockToUI(rootContainer, selectedBlock, index);
});

//=============================================================================================================================================================
//                                                                        4. CONTAINER BLOCK
//=============================================================================================================================================================

function containerBlock(parent, blockInstance) {
    const containerDOM = blockInstance.DOM;
    const containerBlockSelector = containerDOM.querySelector("#block-type-selector");
    
    const containerAddBtn = containerDOM.querySelector(".container-btn");
    containerAddBtn.addEventListener('click', () => {
        let containerSelectedBlock = containerBlockSelector.selectedOptions[0].value;
        const selector = '.inner-' + blockInstance.type;
        const parentContainer = containerDOM.querySelector(selector);
        
        const nestedBlock = addBlockToUI(parentContainer, containerSelectedBlock);
        blockInstance.addChild(nestedBlock);
        console.log(pageRoot);
    })
}