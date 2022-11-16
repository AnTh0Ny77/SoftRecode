

$('.card-recode-cursor').on('click' , function(){
    let value = $(this).children("input[type='hidden']:first").val();
    if (value.length  == 4 ) {
        $('#idTicket').val(value);
        if ($('#idTicket').val().length == 5 ) {
            $('#editTicket').submit();
        }
    }
})