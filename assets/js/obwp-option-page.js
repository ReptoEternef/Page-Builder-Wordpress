const langContainer = document.querySelector('.obwp-option-lang-cont');
const addLanguageBtn = document.querySelector('.obwp-option-lang-cont .obwp-btn');
const langTemplateEl = document.querySelector('.obwp-option-lang-cont .lang-input-template .lang-input-cont');

const items = langContainer.querySelectorAll('.option-item');
console.log(items);

addLanguageBtn.addEventListener('click', () => {
    const newItem = addLangInput();
    const newItemChild = newItem.firstElementChild;

    newItemChild.classList.add('option-item');
    const index = langContainer.querySelectorAll('.option-item').length - 1;
    newItemChild.name = `obwp_options[available_langs][${index}]`;

    console.log(newItemChild);
});




function addLangInput() {
    const newItem = langTemplateEl.cloneNode(true);
    langContainer.appendChild(newItem);
    return newItem;
}
