

$('.card-recode-cursor').on('click' , function(){
    let value = $(this).children("input[type='hidden']:first").val();
    if (value.length == 4 || value.length == 5) {
        $('#idTicket').val(value);
        if ($('#idTicket').val().length == 5 || $('#idTicket').val().length == 4  ) {
            $('#editTicket').submit();
        }
    }
})