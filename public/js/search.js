function dump(v) {

    switch (typeof v) {
        case "object":
            for (var i in v) {
                console.log(i + ":" + v[i]);
            }
            break;
        default: //number, string, boolean, null, undefined
            alert(v);
            console.log(typeof v + ":" + v);
            break;
    }
}

$(function () {

    var input = $('#searchedText');
    $('#searchedText').keyup(function () {
        var formData = 'q=' + $('#searchedText').val();
        $.ajax({
            type: 'GET',
            url: 'https://api.scryfall.com/cards/autocomplete' + input.text(),
            data: formData
        })
            .done(function (response) {
                //dump(response.data);
                var numberOfListItems = response.data.length;
                var listElement = $('#autoUl');

                if (numberOfListItems > 10)
                    numberOfListItems = 10;

                $('#autoUl').empty();

                for (var i = 0; i < numberOfListItems; ++i) {

                    // create an item for each one
                    var listItem = document.createElement('div');
                    listItem.setAttribute('class', 'ss');
                    listItem.onclick = function (event) {
                        $('#searchedText').val(event.target.innerHTML);
                        $('#searchForm').submit();
                    };
                    // Add the item text
                    listItem.innerHTML = response['data'][i];

                    // Add listItem to the listElement
                    $('#autoUl').append(listItem);

                    $('di').addClass('ss');
                }
            })
            .fail(function () {
                $('#autoUl').clear();
            });
    });
});

$(function () {

    var input = $('#searchText');
    $('#searchText').keyup(function () {
        var formData = 'q=' + $('#searchText').val();
        $.ajax({
            type: 'GET',
            url: 'https://api.scryfall.com/cards/autocomplete' + input.text(),
            data: formData
        })
            .done(function (response) {
                //dump(response.data);
                var numberOfListItems = response.data.length;
                var listElement = $('#autoUlSearch');

                if (numberOfListItems > 10)
                    numberOfListItems = 10;

                $('#autoUlSearch').empty();

                for (var i = 0; i < numberOfListItems; ++i) {

                    // create an item for each one
                    var listItem = document.createElement('div');
                    listItem.setAttribute('class', 'ss');
                    listItem.onclick = function (event) {
                        $('#searchText').val(event.target.innerHTML);
                        $('#searchTextHidden').val(event.target.innerHTML);
                        $('#searchForm2').submit();
                    };
                    // Add the item text
                    listItem.innerHTML = response['data'][i];

                    // Add listItem to the listElement
                    $('#autoUlSearch').append(listItem);

                    $('di').addClass('ss');
                }
            })
            .fail(function () {
                $('#autoUlSearch').clear();
            });
    });
});
