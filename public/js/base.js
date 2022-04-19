
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
    $('#action').val(this.innerHTML);
    $('#formCommands').submit();
});
