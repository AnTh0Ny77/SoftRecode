$(document).ready(function() {

//    initialisation table client : 
    let tableClient = $('#client').DataTable({
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
    let tableContact = $("#contactTable").DataTable({
        "paging": false,
        "info":   true,
        retrieve: true,
        deferRender: true,
        "searching": false, 
    })
        
    // fonction selection du client  : 
    $('#client tbody').on('click', 'tr', function () {
        let data = tableClient.row( this ).data();
        $("#choixClient").val(data[0]);
        $("#formSelectClient").submit();
    });

    // fonction selection du contact : 
    $('#contactTable tbody').on('click','tr', function(){
        let text = tableContact.row( this ).data();
        $("#choixContact").val(text[0]);
        $("#formSelectContact").submit();
    })
    
    // Programme d'ajout de ligne dans le devis : 
    //traitement du formulaire : 
    let referenceStricte ;
    $('#choixDesignation option').on('click', function(){
        let referenceStricte = $('#referenceS').val($(this).text());
    });
    // extension de garantie : 
    let xtendMois ; 
    let xtendPrix;
    let ul;
    let li;
    let i;
    $("#xtendGr").on('click', function(){
        if (!$("#xtendPrice").val() && !$("#xtendPrice").val()) {
            $("#xtendPrice").addClass('alert alert-danger');
        }else {
            $("#xtendPrice").removeClass('alert alert-danger');
            xtendPrix = $('#xtendPrice').val();  
            xtendMois = $('#xtendMois').val(); 
            let xtendCouple = [ xtendMois ,  xtendPrix ]; 
             ul = $("#xtendList")
             li =  $('<li></li>').text(xtendCouple[0] + " mois " + xtendCouple[1] + "€ H.T ").addClass('list-group-item col-4 d-flex justify-content-between align-items-center').appendTo(ul);
             i =  $('<i></i>').addClass('fal fa-trash-alt btn btn-link').appendTo(li);
            xtendCouple = [];  
        }
        $(i).on('click', function(){
            $(this).parent().remove();    
        })
    })

    //ajout d'une ligne de devis :
    $("#addRow").on('click', function(){
        let row = []; 
        let prestation = $("#prestationChoix").val();
        row.push(prestation);
        let designation = $("#referenceS").val();
        row.push(designation);
        let etat = $("#etatRow").val();
        row.push(etat);
        let garantie = $("#garantieRow").val();
        row.push( garantie);
        let quantite =  $("#quantiteRow").val();
        row.push( quantite);
        let prix = $("#prixRow").val();
        row.push( prix);
        let comClient = $("#comClient").val();
        row.push(comClient);
        let comInterne = $("#comInterne").val();
        row.push(comInterne);
        let xtend = [];
        
        console.log(row);
    })
  
        
   
    
} );