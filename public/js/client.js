$(document).ready(function() {

//    initialisation table client : nouveauDevis.php
    var tableClient = $('#client').DataTable({
        "paging": true,
        "info":   true,
        retrieve: true,
        deferRender: true,
        "searching": true,   
    });

   // initialisation table devis : nouveauDevis.php
    $('#Devis').DataTable({
        "paging": false,
        "info":   false,
        "searching": false,
        deferRender:  true,
        rowReorder: true
    });

    //initialisation table contact  
    var tableContact = $("#contactTable").DataTable({
        "paging": false,
        "info":   true,
        retrieve: true,
        deferRender: true,
        "searching": false, 
    })
        
    // fonction selection du client  : nouveauDevis.php
    $('#client tbody').on('click', 'tr', function () {
        var data = tableClient.row( this ).data();
        $("#choixClient").val(data[0]);
        $("#formSelectClient").submit();
    });

    // fonction selection du contact : nouveauDevis.php
    $('#contactTable tbody').on('click','tr', function(){
        var text = tableContact.row( this ).data();
        $("#choixContact").val(text[0]);
        $("#formSelectContact").submit();
    })
    
    
} );