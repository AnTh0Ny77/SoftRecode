$(document).ready(function() {

    //init des tooltips:
    $(function () {
        $('[data-toggle="tooltip"]').tooltip()
    })


    $('#printButton').on('click' , function()
    {
        window.print();
    })
    

})