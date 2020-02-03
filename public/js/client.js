$(document).ready(function() {

//    initialisation table client : 
    var tableClient = $('#client').DataTable({
        "paging": true,
        "info":   true,
        retrieve: true,
        deferRender: true,
        "searching": true,   
    });

   // initialisation table devis : 
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
        
    // fonction selection du client  : 
    $('#client tbody').on('click', 'tr', function () {
        var data = tableClient.row( this ).data();
        $("#choixClient").val(data[0]);
        $("#formSelectClient").submit();
    });

    // fonction selection du contact : 
    $('#contactTable tbody').on('click','tr', function(){
        var text = tableContact.row( this ).data();
        $("#choixContact").val(text[0]);
        $("#formSelectContact").submit();
    })
    
    // Programme d'ajout de ligne dans le devis : 
    //traitement du formulaire : 
    var referenceStricte ;
    $('#choixDesignation option').on('click', function(){
        var referenceStricte = $('#referenceS').val($(this).text());
    });
    // extension de garantie : 
    var xtendMois ; 
    $("#xtendMois ").change(function(){
        var xtendMois = $(this).val();
    })
    var xtendPrix;
    $("#xtendGr").on('click', function(){
        xtendMois =  $("#xtendMois").$(this).children("option:selected").val();
        
        console.log(xtendMois);
    })
    
} );