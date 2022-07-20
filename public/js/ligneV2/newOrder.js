$(document).ready(function () {
    $('.select-order').on('change' , function(){
        let value = this.name;
        let order = $(this).children("option:selected").val();
        $('#valueNewOrder').val(order);
        $('#idNewOrder').val(value);
        $('#newOrderForm').submit();
    })
})