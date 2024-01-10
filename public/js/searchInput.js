class SearchInput {
    constructor(inputSelector) {
        this.inputSelector = inputSelector;
        this.KEY_ENTER = 13;
        this.KEY_UP = 38;
        this.KEY_DOWN = 40;
        const inputElement = document.querySelector(this.inputSelector);
        this.API_URL = inputElement.dataset.apiUrl;
        this.callbackFunctionName = inputElement.dataset.callback;
        this.selected = null;
        this.timeout = null;
        this.lastRequest = 0;
        this.searchResultsCache = {};
        this.blurTimeout = null;

        document.querySelector(this.inputSelector)
            .addEventListener('keydown', (e) => this.handleKeydown(e));
        document.querySelector(this.inputSelector)
            .addEventListener('keyup', (e) => this.handleKeyup(e));
        document.querySelector(this.inputSelector)
            .addEventListener('blur', (event) => {
                setTimeout(() => { // Defer the clearance, hence allowing for the click event to propagate
                    const relatedTarget = document.activeElement;
                    if (relatedTarget !== this.dropdownElement &&
                        (this.dropdownElement === null ||
                            !this.dropdownElement.contains(relatedTarget))) {
                        this.clearDropdown();
                    }
                }, 100);
            });
        inputElement.addEventListener('focus', () => {
            if(inputElement.value) {
                this.fillSearchDiv(inputElement);
            }
        });
    }

    handleKeydown(e) {
        if(e.keyCode === this.KEY_ENTER)
            e.preventDefault();
        this.navigateDropDown(e);
    }

    handleKeyup(e) {
        clearTimeout(this.timeout);
        this.timeout = setTimeout(() => {
            // If the input is empty, clear the dropdown
            if(e.target.value === '') {
                this.clearDropdown();
            }
            // Else, if dropdown is not present, and key is not up or down
            else if (
                e.keyCode !== this.KEY_UP &&
                e.keyCode !== this.KEY_DOWN) {
                this.clearDropdown();
                this.fillSearchDiv(e.target);
            }
        }, 300);
    }

    navigateDropDown(e) {
        if(!this.dropdownElement) return;
        const items = Array.from(this.dropdownElement.getElementsByTagName('a'));
        if(items.length === 0) return;

        if (e.keyCode === this.KEY_ENTER && this.selected) {
            this.selected.click();
        }
        if (e.keyCode === this.KEY_UP) {
            if (!this.selected || this.selected !== items[0]) {
                this.updateSelectedItemSelection(false);
            }
        } else if(e.keyCode === this.KEY_DOWN) {
            if (!this.selected || this.selected !== items[items.length - 1]) {
                this.updateSelectedItemSelection(true);
            }
        }
    }

    updateSelectedItemSelection(isNextElement){
        let items = Array.from(this.dropdownElement.getElementsByTagName('a'));

        if(this.selected) {
            this.selected.classList.remove('selectedItem');
        }

        if(isNextElement) {
            if(!this.selected || this.selected === items[items.length - 1]) {
                this.selected = items[0];
            } else {
                this.selected = this.selected.nextElementSibling;
            }
        } else {
            if(!this.selected || this.selected === items[0]) {
                this.selected = items[items.length - 1];
            } else {
                this.selected = this.selected.previousElementSibling;
            }
        }

        this.selected.classList.add('selectedItem');
    }

    fillSearchDiv(source){
        let thisRequest = ++this.lastRequest;
        const inputValue = source.value;
        if (this.searchResultsCache[inputValue]) {
            // Use cached data to fill dropdown
            this.populateDropdown(this.searchResultsCache[inputValue], thisRequest);
        }
        else {
            const formData = 'q=' + source.value;
            fetch(this.API_URL + formData, {method: 'GET'})
                .then(response => response.json())
                .then((data) => {
                    this.populateDropdown(data, thisRequest);
                    this.searchResultsCache[inputValue] = data;
                }).catch(() => {
                if (this.dropdownElement) this.dropdownElement.innerHTML = "";
            });
        }
    }
    populateDropdown(data,thisRequest) {

        if (thisRequest === this.lastRequest) {
            this.selected = undefined;

            // Create and show dropdown
            this.dropdownElement = document.createElement('div');
            this.dropdownElement.classList.add('show');
            this.dropdownElement.classList.add('search-input-dropdown');
            this.dropdownElement.style.position = "absolute";

            let numberOfListItems = data.data.length;
            numberOfListItems = numberOfListItems > 10 ? 10 : numberOfListItems;
            this.dropdownElement.innerHTML = "";

            for (let i = 0; i < numberOfListItems; ++i) {
                let selectItem = document.createElement('a');
                selectItem.classList.add('search-input-dropdown-item');
                selectItem.innerHTML = data['data'][i];
                selectItem.addEventListener('mouseenter', () => {
                    if (this.selected) {
                        this.selected.classList.remove('selectedItem');
                    }
                    this.selected = selectItem;
                    this.selected.classList.add('selectedItem');
                });
                selectItem.onclick = (e) => {
                    const selectedValue = event.target.textContent;
                    document.querySelector(this.inputSelector).value = selectedValue;
                    const callbackFunction = window[this.callbackFunctionName];
                    if (typeof callbackFunction === "function") {
                        callbackFunction(e.target.innerHTML);
                    }
                }
                this.dropdownElement.appendChild(selectItem);
            }

            // Append dropdown to parent of input
            document.querySelector(this.inputSelector).parentElement.appendChild(this.dropdownElement);
        }
    }
    clearDropdown() {
        if(this.dropdownElement) {
            this.dropdownElement.parentElement.removeChild(this.dropdownElement);
            this.dropdownElement = null;
        }
    }
}
