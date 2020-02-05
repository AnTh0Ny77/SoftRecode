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
   let devisTable =  $('#Devis').DataTable({
        "paging": false,
        "info":   false,
        "searching": false,
        rowReorder: true,
    });
   
    
    /* Formatting function for row details  */
    function format ( d ) {
   
    return '<table cellpadding="5" cellspacing="0" style="border-bottom: thick double #32a1ce;"  style="padding-left:50px;">'+
        '<tr>'+
            '<td> Extensions de Garantie:</td>'+
            '<td>'+d[0]+'</td>'+
        '</tr>'+
        
    '</table>';
    }
    
    
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
    let xtendArray = [];
   
    $("#xtendGr").on('click', function(){
        if (!$("#xtendPrice").val() && !$("#xtendPrice").val()) {
            $("#xtendPrice").addClass('alert alert-danger');
        }else {
            $("#xtendPrice").removeClass('alert alert-danger');
            $("#xtendList").empty();
            xtendPrix = $('#xtendPrice').val();  
            xtendMois = $('#xtendMois').val(); 
            let  xtendCouple = [ xtendMois , xtendPrix ]; 
            xtendArray.push(xtendCouple);
            for (let index = 0; index < xtendArray.length; index++) {
                let ul = $("#xtendList");
                let li =  $('<li></li>').text(xtendArray[index][0] + " mois " + xtendArray[index][1] + "€ H.T ").addClass('list-group-item col-4 d-flex justify-content-between align-items-center').appendTo(ul);
                let  i =  $('<i></i>').addClass('fal fa-trash-alt btn btn-link deleteParent').val(index).appendTo(li);
               
            }
             xtendCouple = [];
        }   
    })

    $("#xtendList").on('click', '.deleteParent' ,function(){
        xtendArray.splice(parseInt($(this).val()),1);
        $("#xtendList").empty();
        for (let index = 0; index < xtendArray.length; index++) {
            let ul = $("#xtendList");
            let li =  $('<li></li>').text(xtendArray[index][0] + " mois " + xtendArray[index][1] + "€ H.T ").addClass('list-group-item col-4 d-flex justify-content-between align-items-center').appendTo(ul);
            let  i =  $('<i></i>').addClass('fal fa-trash-alt btn btn-link deleteParent').val(index).appendTo(li);
            console.log(xtendArray);
        }
    })
       
    //ajout d'une ligne de devis :
    counter = 1 ;
    $("#addRow").on('click', function(){
        let row = []; 
        let modify = '<i class="fal fa-edit btn"></i>';
        row.push(counter);
        row.push(modify);
        let prestation = $("#prestationChoix").val();
        row.push(prestation);
        let designation = $("#referenceS").val();
        row.push(designation);
        let etat = $("#etatRow").val();
        row.push(etat);
        let garantie = $("#garantieRow").val() + " mois ";
        row.push( garantie);
        let quantite =  $("#quantiteRow").val()  ;
        row.push( quantite);

        let prix;
        if ($("#barrePrice").val().length > 0) {
             prix =  ' <s>' + $("#barrePrice").val()  + "€</s> " + $("#prixRow").val() + " €" ;
        }else {prix =  $("#prixRow").val() + " €" ;};
        row.push( prix);
       
        
        let comClient = $("#comClient").val();
        
        let comInterne = $("#comInterne").val();
        
        let prixBarre = $("#barrePrice").val();
        
        let deleteButton = "<i class='fal fa-trash-alt btn deleteRow'></i>";
        row.push(deleteButton);
       
       
        devisTable.row.add(row).draw( false );
        devisTable.rows().every(function(){
            if(!this.child.isShown()){
                this.child(format(this.data())).show();
                $(this.node()).addClass('');
        }});
        devisTable.row().child( format(row)).show();
        row = [];
        xtendArray = [];
        counter ++;
        
    })




     // efface sa propre ligne : 
     devisTable.on('click','.deleteRow',function() {
        if ( $(this).parents('tr').hasClass('selected') ) {
            $(this).parents('tr').removeClass('selected');
        }
        else {
            devisTable.$('tr.selected').removeClass('selected');
            $(this).parents('tr').addClass('selected');
        }
        devisTable.row('.selected').remove().draw( false );
     });
     // update sa propre ligne : 

        
    
} );