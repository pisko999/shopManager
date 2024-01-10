
class SearchInput {
    constructor(inputSelector) {
        this.inputSelector = inputSelector;
        this.selected = null;
        this.timeout = null;
        this.lastRequest = 0;
        this.API_URL = 'https://api.scryfall.com/cards/autocomplete?';
        document.querySelector(this.inputSelector)
            .addEventListener('keydown', (e) => this.handleKeydown(e));
        document.querySelector(this.inputSelector)
            .addEventListener('keyup', (e) => this.handleKeyup(e));
    }

    handleKeydown(e) {
        if(e.keyCode === KEY_ENTER)
            e.preventDefault();
        this.navigateDropDown(e.keyCode, '#autoCompleteCard', '#searchCardForm');
    }

    handleKeyup(e) {
        clearTimeout(this.timeout);
        this.timeout = setTimeout(() => {
            if(!(Array.from(document.querySelectorAll('#autoCompleteMain a')).length > 0) &&
                (e.keyCode != KEY_UP && e.keyCode != KEY_DOWN)) {
                this.fillSearchDiv(e.target, "#searchDropdown", "#autoCompleteMain", '#searchFormMain');
            }
        }, 300);
    }

    navigateDropDown(key, target, form = null){
        // navigateDropDown implementation...
    }

    fillSearchDiv(source, dropdown, target, form){
        // fillSearchDiv implementation...
    }
}


function dump(v) {
    switch (typeof v) {
        case "object":
            for (const i in v) {
                console.log(i + ":" + v[i]);
            }
            break;
        default:
            alert(v);
            console.log(typeof v + ":" + v);
            break;
    }
}
const KEY_ENTER = 13;
const KEY_UP = 38;
const KEY_DOWN = 40;
const API_URL = 'https://api.scryfall.com/cards/autocomplete?';

function updateSelectedItemSelection(isNextElement){
    selected.classList.remove('selectedItem');
    selected = isNextElement ? selected.nextElementSibling : selected.previousElementSibling;
    selected.classList.add('selectedItem');
}
let selected;
function navigateDropDown(key, target, form = null){
    const items = Array.from(document.querySelectorAll(target + ' a'));
    if(items.length === 0) return;

    if (key === KEY_ENTER) {
        if (selected) {
            selected.click();
        } else if (form) {
            document.querySelector(form).submit();
        }}
    if (key === KEY_UP) {
        if (!selected || selected !== items[0]) {
            updateSelectedItemSelection(false);
        }
    } else if(key === KEY_DOWN) {
        if (!selected || selected !== items[items.length - 1]) {
            updateSelectedItemSelection(true);
        }
    }
}
let timeout = null;


function fillSearchDiv(source, dropdown , target, form){
    let thisRequest = ++lastRequest;

    var formData = 'q=' + source.value;
    fetch(API_URL + formData, {
        method: 'GET'})
        .then(response => response.json())
        .then(data => {
            if(thisRequest === lastRequest) {
                selected = undefined;

                document.querySelector(target).classList.add('show');

                let numberOfListItems = data.data.length;
                let listElement = document.querySelector(target);

                numberOfListItems = numberOfListItems > 10 ? 10 : numberOfListItems;

                listElement.innerHTML = "";
                listElement.classList.remove('d-none');

                for(let i=0; i < numberOfListItems; ++i){
                    let selectItem = document.createElement('a');
                    selectItem.classList.add('dropdown-item');
                    selectItem.innerHTML = data['data'][i];
                    selectItem.onclick = function(e){
                        document.querySelector(form + " input[type='text'], input[type='hidden'").value = e.target.innerHTML;
                        document.querySelector(form).submit();
                    }
                    listElement.appendChild(selectItem);
                }
            }
        }).catch(() => listElement.innerHTML = "");
}
