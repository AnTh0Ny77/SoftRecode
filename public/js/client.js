$(document).ready(function() {
   
   // initialisation table devis
    $('#Devis').DataTable({
        "paging": false,
        "info":   false,
        "searching": false,
        rowReorder: true
    });
   // initialisation table client 
    var tableClient = $('#client').DataTable({
        "paging": true,
        "info":   true,
        "searching": true,
       
    });
    
    // fonction selection du client  
    $('#client tbody').on('click', 'tr', function () {
        var data = tableClient.row( this ).data();
        $("#selectClient").text(data[1] + " " + data[2] + " " + data[3] + " " + data[4] );
        $("#choixClient").val(data[0]);
        console.log( $("#choixClient").val()); 
    } );
    
} );