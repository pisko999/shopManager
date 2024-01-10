
function getValueOf(data) {
    return (data != undefined ?
        data :
        '');
}

$('#chbSelectAll').change(function (t) {
    s = this;
    $('.chbCommandId').each(function (e){
        this.checked = s.checked;
    });
});
$('.actions').click(function (){
    $('#action').val(this.dataset.action);
    $('#formCommands').submit();
});

$('.giftItemAdd').click(function(e){
    let row = this.closest('.row');
    let quantity = row.getElementsByClassName('inputQuantity')[0].value;
    let foil = row.getElementsByClassName('selectFoil')[0].selectedOptions[0].value;
    e.preventDefault();
    $.ajax({
        method: "POST",
        url: this.dataset.url,
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        data: {
            'quantity': quantity,
            'foil': foil
        },
        success: function (answer) {
            toastr.success("Gift item added<br>Quantity: " + answer.quantity);
        },
        error: function (answer) {
            console.log(answer);
            alert('! Something went wrong !');
        }
    });
})

$('.deleteGiftItem').click(function() {
    let row = this.closest('tr');
    $.ajax({
        method:"GET",
        url: this.dataset.url,
        success: function (answer) {
            if(answer) {
                toastr.success("Item deleted");
                row.remove();
            } else {
                toastr.error('Something went wrong');
            }
        }
    })
})
$('a[data-toggle="tooltip"]').tooltip({
    animated: 'fade',
    placement: 'bottom',
    html: true
});
