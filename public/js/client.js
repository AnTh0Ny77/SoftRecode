$(document).ready(function() {
   
   // initialisation table devis : nouveauDevis.php
    $('#Devis').DataTable({
        "paging": false,
        "info":   false,
        "searching": false,
        rowReorder: true
    });
   // initialisation table client : nouveauDevis.php
    var tableClient = $('#client').DataTable({
        "paging": true,
        "info":   true,
        "searching": true,
         
       
    });
    
    // fonction selection du client  : nouveauDevis.php
    $('#client tbody').on('click', 'tr', function () {
        var data = tableClient.row( this ).data();
        $("#selectClient").text(data[1] + " " + data[2] );
        $("#choixClient").val(data[0]);
    });


    
    
} );