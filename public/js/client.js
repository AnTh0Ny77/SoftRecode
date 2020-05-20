
$(document).ready(function() {

let tableClient;
let tableLivraison;
let tableContact;

//appel ajax au click table client : 
$('#AjaxClient').on('click', function(){
    let dataSet = [];

    $.ajax({
        type: 'post',
        url: "AjaxSociete",
        data : {"AjaxSociete" : 7},
        
    success: function(data){
        dataSet = JSON.parse(data);
        $('#modalClient').modal('show');
        tableClient = $('#client').DataTable({
            "language": {
                "decimal":        "",
                "emptyTable":     "aucuns résultats",
                "info":           "Voir _START_ to _END_ of _TOTAL_ résultats",
                "infoEmpty":      "Voir 0 to 0 of 0 résultats",
                "infoFiltered":   "(filtré dans _MAX_ total résultats)",
                "infoPostFix":    "",
                "thousands":      ",",
                "lengthMenu":     "Voir _MENU_ résultats par pages",
                "loadingRecords": "Loading...",
                "processing":     "Processing...",
                "search":         "Recherche:",
                "zeroRecords":    "Aucun résultats",
                "paginate": {
                    "first":      "Première",
                    "last":       "Dernière",
                    "next":       "Suivante",
                    "previous":   "Précédente"
                }
                  
                    
            },
        data: dataSet,
        "columns": [
        {"data": "client__id"},
        {"data": "client__societe"},
        {"data": "client__cp"},
        {"data": "client__ville"}], 
        
        "paging": true,
        "info":   true,
        retrieve: true,
        "deferRender": true,
        "searching": true,});
        },

    error: function (err) {
    alert('error: ' + err);}
    })
})




    

    //appel ajax au click table livraison : 
    $('#buttonLivraison').on('click', function(){
        let dataSet = [];
        $.ajax({
            type: 'post',
            url: "AjaxSociete",
            data : 
            {"AjaxLivraison" : 7},
                
            success: function(data){
                dataSet = JSON.parse(data);
                $('#ModalLivraison').modal('show'); 
                tableLivraison = $('#Livraison').DataTable({
                    "language": {
                        "decimal":        "",
                        "emptyTable":     "aucuns résultats",
                        "info":           "Voir _START_ to _END_ of _TOTAL_ résultats",
                        "infoEmpty":      "Voir 0 to 0 of 0 résultats",
                        "infoFiltered":   "(filtré dans _MAX_ total résultats)",
                        "infoPostFix":    "",
                        "thousands":      ",",
                        "lengthMenu":     "Voir _MENU_ résultats par pages",
                        "loadingRecords": "Loading...",
                        "processing":     "Processing...",
                        "search":         "Recherche:",
                        "zeroRecords":    "Aucun résultats",
                        "paginate": {
                            "first":      "Première",
                            "last":       "Dernière",
                            "next":       "Suivante",
                            "previous":   "Précédente"
                        }
                          
                            
                    },
                   
                data: dataSet,
               
                "columns": [
                {"data": "client__id"},
                {"data": "client__societe"},
                {"data": "client__cp"},
                {"data": "client__ville"} 
                ], 
                
                "paging": true,
                "info":   true,
                retrieve: true,
                "deferRender": true,
                "searching": true,}
            );},      
                
            error: function (err) {
            alert('error: ' + err);
            }})   
    })


    // fonction selection de l'adresse de livraison  : 
    $('#Livraison tbody').on('click', 'tr', function () {
    let donne = tableLivraison.row( this ).data();
    $.ajax({
        type: 'post',
        url: "choixLivraison",
        data : 
        {
            "AjaxLivraison" :  donne.client__id
        },
        success: function(data){  
        dataSet = JSON.parse(data);
        let content = showClient(dataSet);
        $('#textLivraison').html(content);
        $('#livraisonSelect').val(dataSet.client__id);
        $('#contactDivLVR').html("Aucun Contact");
        $('#contact_livraison').val("");
        $('#spanLivraison').removeClass('d-none');
        $('#spanLivraison').text(dataSet.client__societe);
        $('#ModalLivraison').modal('hide');
        $('#toogleContactLVR').removeAttr('disabled');
        $('#toogleContactCreaLVR').removeAttr('disabled');
        },
        error: function (err) {
            alert('error: ' + err);
        }
    })
    });


    //fonction qui affiche le ptit check vert de l'adresse de livraison: 
    let checkVert = function (){
        if ($('#livraisonSelect').val()) {
            $('#petitCheck').removeAttr('hidden');
        } else {
            $('#petitCheck').prop("hidden", true);
        }
    }
    checkVert();

    // int de la table contact :
    tableContact = $('#contactTable').DataTable({
        "language": {
            "decimal":        "",
            "emptyTable":     "aucuns résultats",
            "info":           "Voir _START_ to _END_ of _TOTAL_ résultats",
            "infoEmpty":      "Voir 0 to 0 of 0 résultats",
            "infoFiltered":   "(filtré dans _MAX_ total résultats)",
            "infoPostFix":    "",
            "thousands":      ",",
            "lengthMenu":     "Voir _MENU_ résultats par pages",
            "loadingRecords": "Loading...",
            "processing":     "Processing...",
            "search":         "Recherche:",
            "zeroRecords":    "Aucun résultats",
            "paginate": {
                "first":      "Première",
                "last":       "Dernière",
                "next":       "Suivante",
                "previous":   "Précédente"
            }
              
                
        },
    "columns": [
    {"data": "contact__id"},
    {"data": "contact__nom"},
    {"data": "kw__lib"}],  
    "paging": true,
    "info":   true,
    "deferRender": true,
    retrieve: true,
    deferRender: true,
    "searching": false,      
    });





  




    // int de la table contact Livraison :
    tableContactLVR = $('#contactTableLVR').DataTable({
        "language": {
            "decimal":        "",
            "emptyTable":     "aucuns résultats",
            "info":           "Voir _START_ to _END_ of _TOTAL_ résultats",
            "infoEmpty":      "Voir 0 to 0 of 0 résultats",
            "infoFiltered":   "(filtré dans _MAX_ total résultats)",
            "infoPostFix":    "",
            "thousands":      ",",
            "lengthMenu":     "Voir _MENU_ résultats par pages",
            "loadingRecords": "Loading...",
            "processing":     "Processing...",
            "search":         "Recherche:",
            "zeroRecords":    "Aucun résultats",
            "paginate": {
                "first":      "Première",
                "last":       "Dernière",
                "next":       "Suivante",
                "previous":   "Précédente"
            }
              
                
        },
    "columns": [
    {"data": "contact__id"},
    {"data": "contact__nom"},
    {"data": "kw__lib"}],  
    "paging": true,
    "info":   true,
    "deferRender": true,
    retrieve: true,
    deferRender: true,
    "searching": false,      
    });





     //appel data  table contact Livraison: 
     $('#toogleContactLVR').on('click', function(){
        let dataSetContact = [];
        $.ajax({
            type: 'post',
            url: "tableContact",
            data : 
            {
                "AjaxContactTable" : $('#livraisonSelect').val()
            },
            success: function(data){
                tableContactLVR.clear().draw();
                dataSetContact = JSON.parse(data);
                $('#modalContactLVR').modal('show');
               tableContactLVR.rows.add(dataSetContact).draw(); 
               dataSetContact = [];
            },
            error: function (err) {
                alert('error: ' + err);
            }
        })
    })


     //appel ajax au choix du contact livraison : 
     $('#contactTableLVR tbody').on('click', 'tr', function () {
        
        let donne = tableContactLVR.row( this ).data();
        $.ajax({
            type: 'post',
            url: "choixContact",
            data : 
            {
                "AjaxContact" :  donne.contact__id
            },
            success: function(data){  
            dataSet = JSON.parse(data);
            let content = showContact(dataSet)
            $('#contactDivLVR').html(content);
            $('#contact_livraison').val(dataSet.contact__id);
            $('#modalContactLVR').modal('hide');
            },
            error: function (err) {
                alert('error: ' + err);
            }

        })
    
    });




    //appel data  table contact : 
    $('#toogleContact').on('click', function(){
        let dataSetContact = [];
        $.ajax({
            type: 'post',
            url: "tableContact",
            data : 
            {
                "AjaxContactTable" : $('#clientSelect').val()
            },
            success: function(data){
                tableContact.clear().draw();
                dataSetContact = JSON.parse(data);
                $('#modalContact').modal('show');
               tableContact.rows.add(dataSetContact).draw(); 
               dataSetContact = [];
            },
            error: function (err) {
                alert('error: ' + err);
            }
        })
    })


    //appel ajax au choix du contact : 
    $('#contactTable tbody').on('click', 'tr', function () {
        let donne = tableContact.row( this ).data();
        $.ajax({
            type: 'post',
            url: "choixContact",
            data : 
            {
                "AjaxContact" :  donne.contact__id
            },
            success: function(data){  
            dataSet = JSON.parse(data);
            let content = showContact(dataSet)
            $('#contactDiv').html(content);
            $('#contactSelect').val(dataSet.contact__id);
            $('#modalContact').modal('hide');
            },
            error: function (err) {
                alert('error: ' + err);
            }

        })
    
    });

    //appel ajax a la creation du contact : 
    $('#postContact').on('click', function () {
        $.ajax({
            type: 'post',
            url: "createContact",
            data : 
            {
                "societeLiaison" : $('#clientSelect').val(),
                "inputStateContact" : $('#inputStateContact').val() ,
                "inputCiv" : $('#inputCiv').val(),
                "nomContact" :  $('#nomContact').val(),
                "prenomContact" : $('#prenomContact').val(),
                "telContact" : $('#telContact').val(),
                "faxContact" : $('#faxContact').val(),
                "mailContact" : $('#mailContact').val(),
            },
            success: function(data){
            dataSetCreaContact = JSON.parse(data);
            let content = showContact(dataSetCreaContact)
            $('#contactDiv').html(content);
            $('#contactSelect').val(dataSetCreaContact.contact__id);
            $('#modalContactCrea').modal('hide');
            },
            error: function (err) {
                alert('error: ' + err);
            }

        })
    
    });




    





     //appel ajax a la creation du contact Livraison : 
     $('#postContactLVR').on('click', function () {
        $.ajax({
            type: 'post',
            url: "createContact",
            data : 
            {
                "societeLiaison" : $('#livraisonSelect').val(),
                "inputStateContact" : $('#inputStateContactLVR').val() ,
                "inputCiv" : $('#inputCivLVR').val(),
                "nomContact" :  $('#nomContactLVR').val(),
                "prenomContact" : $('#prenomContactLVR').val(),
                "telContact" : $('#telContactLVR').val(),
                "faxContact" : $('#faxContactLVR').val(),
                "mailContact" : $('#mailContactLVR').val(),
            },
            success: function(data){
            dataSetCreaContact = JSON.parse(data);
            let content = showContact(dataSetCreaContact);
            $('#contactDivLVR').html(content);
            $('#contact_livraison').val(dataSetCreaContact.contact__id);
            $('#modalContactCreaLVR').modal('hide');
            
            },
            error: function (err) {
                alert('error: ' + err);
            }

        })
    
    });


    // appel ajax choix du client : 
    $('#client tbody').on('click', 'tr', function () {
        $('#contactDiv').html(" Aucun contact");
        $('#contactSelect').val("");
        $('#spanLivraison').html('');
        $('#spanLivraison').addClass('d-none');
        $('#contactDivLVR').html("Aucun contact");
        $('#contact_livraison').val("");
        $('#textLivraison').text("Livré à la meme adresse");
        $('#livraisonSelect').val("");
        let donne = tableClient.row( this ).data();
        $.ajax({
            type: 'post',
            url: "createNew",
            data : 
            {
                "AjaxClient" :  donne.client__id
            },
            success: function(data){
            dataSet = JSON.parse(data);
            let content = showClient(dataSet);
            $('#divClient').html(content);
            $('#clientSelect').val(dataSet.client__id);
            $('#spanSociete').removeClass('d-none');
            $('#spanSociete').text(dataSet.client__societe);
            $('#modalClient').modal('hide');
            $('#addNewRow').removeAttr('disabled');
            $('#toogleContact').removeAttr('disabled');
            $('#buttonLivraison').removeAttr('disabled');
            $('#buttonCrealivraison').removeAttr('disabled');
            $('#toogleCreaContact').removeAttr('disabled');
            
            
            },
            error: function (err) {
                alert('error: ' + err);
            }
        })
    });

    



    //appel ajax creation de client:  
    $('#PostClient').on('click', function(){
        $('#contactDiv').html(" Aucun contact");
        $('#contactSelect').val("");
        $('#spanLivraison').html('');
        $('#spanLivraison').addClass('d-none');
        $('#contactDivLVR').html("Aucun contact");
        $('#contact_livraison').val("");
        $('#textLivraison').text("Livré à la meme adresse");
        $('#livraisonSelect').val("");
        
        $.ajax({
            type: 'post',
            url: "createClient",
            data : 
            {
                "inputAddress" : $('#inputAddress').val() ,
                "inputAddress2" : $('#inputAddress2').val(),
                "societeNameCreate" :  $('#societeNameCreate').val(),
                "inputZip" : $('#inputZip').val(),
                "inputCity" : $('#inputCity').val(),
                "inlineFormCustomSelect" : $('#SelectClientCountry').val(),
            },
            success: function(data){
            dataSetCrea = JSON.parse(data);
            let content = showClient(dataSetCrea);
            $('#divClient').html(content);
            $('#modalClientCrea').modal('hide');
            $('#spanSociete').removeClass('d-none');
            $('#spanSociete').text(dataSetCrea.client__societe);
            $('#toogleContact').removeAttr('disabled');
            $('#buttonLivraison').removeAttr('disabled');
            $('#buttonCrealivraison').removeAttr('disabled');
            $('#toogleCreaContact').removeAttr('disabled');
            
            },
            error: function (err) {
                alert('error: ' + err);
            }

        })

    })


    // check la valeur des inputs et disabled buttons a l'ouverture de la page: 
    let checkIfModify = function(){
        if ($('#clientSelect').val()) {
            if ($('#clientSelect').val().length > 0 ) {
                $('#toogleContact').removeAttr('disabled');
                $('#buttonLivraison').removeAttr('disabled');
                $('#buttonCrealivraison').removeAttr('disabled');
                $('#toogleCreaContact').removeAttr('disabled');
            }
        }
        
    }
    checkIfModify();

    // check la valeur des inputs de livraison a l'ouverture de la page : 
    let checkIfLivraison = function(){
        if ($('#livraisonSelect').val()) {
            if ($('#livraisonSelect').val().length > 0 ) {
                $('#toogleContactLVR').removeAttr('disabled');
                $('#toogleContactCreaLVR').removeAttr('disabled');
            }
        }   
    }

    checkIfLivraison();



     //appel ajax creation de societe livrée:  
     $('#PostSocieteLivraison').on('click', function(){
        $('#livraisonSelect').val("");
        $.ajax({
            type: 'post',
            url: "createClient",
            data : 
            {
                "inputAddress" : $('#inputAddressLVR').val() ,
                "inputAddress2" : $('#inputAddress2LVR').val(),
                "societeNameCreate" :  $('#societeNameCreateLVR').val(),
                "inputZip" : $('#inputZipLVR').val(),
                "inputCity" : $('#inputCityLVR').val(),
                "inlineFormCustomSelect" : $('#SelectClientCountryLVR').val(),
            },
            success: function(data){
            dataSetCrea = JSON.parse(data);
            $('#textLivraison').html(dataSetCrea.client__societe + '<br>' + dataSetCrea.client__adr1 + '<br>' + dataSetCrea.client__ville);
            $('#livraisonSelect').val(dataSetCrea.client__id);
            $('#modalSocieteLivraison').modal('hide');
            $('#spanLivraison').removeClass('d-none');
            $('#spanLivraison').text(dataSetCrea.client__societe);
            $('#toogleContactLVR').removeAttr('disabled');
            $('#toogleContactCreaLVR').removeAttr('disabled');
            },
            error: function (err) {
                alert('error: ' + err);
            }

        })

    })
   
    

    // fonction post du formulaire certificateNew : 

    $("#certificateNew").on('click', function() {
        $("#formCertificate").submit();
    })
 

    // initi table mesDevis : 
    let modifDevis = $('#MyDevis').DataTable({
        "language": {
            "decimal":        "",
            "emptyTable":     "aucuns résultats",
            "info":           "Voir _START_ to _END_ of _TOTAL_ résultats",
            "infoEmpty":      "Voir 0 to 0 of 0 résultats",
            "infoFiltered":   "(filtré dans _MAX_ total résultats)",
            "infoPostFix":    "",
            "thousands":      ",",
            "lengthMenu":     "Voir _MENU_ résultats par pages",
            "loadingRecords": "Loading...",
            "processing":     "Processing...",
            "search":         "Recherche:",
            "zeroRecords":    "Aucun résultats",
            "paginate": {
                "first":      "Première",
                "last":       "Dernière",
                "next":       "Suivante",
                "previous":   "Précédente"
            }
              
                
        },
        "columnDefs": [
            
            {"className": "dt-center", "targets": 4},
            {"targets": [ 1 ], "visible": false},
            {"targets": [ 0 ], "visible": false},
           
            
          ],
        "order": [[ 1, "desc" ] ],
        "paging": true,
         "info":   false,
         "pageLength": 10,
        retrieve: true,
        "deferRender": true,
        "searching": false,  
        
       
    })

    // ini table commandes :
    let validCmd = $('#MyCommande').DataTable({
        "language": {
            "decimal":        "",
            "emptyTable":     "aucuns résultats",
            "info":           "Voir _START_ to _END_ of _TOTAL_ résultats",
            "infoEmpty":      "Voir 0 to 0 of 0 résultats",
            "infoFiltered":   "(filtré dans _MAX_ total résultats)",
            "infoPostFix":    "",
            "thousands":      ",",
            "lengthMenu":     "Voir _MENU_ résultats par pages",
            "loadingRecords": "Loading...",
            "processing":     "Processing...",
            "search":         "Recherche:",
            "zeroRecords":    "Aucun résultats",
            "paginate": {
                "first":      "Première",
                "last":       "Dernière",
                "next":       "Suivante",
                "previous":   "Précédente"
            }
              
                
        },
        "order": [[ 2, "asc" ] , [4 ,"desc"]],
        "paging": true,
         "info":   false,
        retrieve: true,
        "deferRender": true,
        "searching": false,  
       
    })

    // disable buttons multiple si pas de ligne select dans la table mes devis:  
    let checkClassMulti = function(){
        let RowModif =  $('#MyDevis').find('tr');
         if (RowModif.hasClass('selected')) {
             $('.multiButton').removeAttr('disabled');
         } else {
             $('.multiButton').prop("disabled", true);
         }
      }

   

 // disable buttons multiple si pas de ligne select dans la table commandes:
    let checkClassCmd = function(){
        let RowModif =  $('#MyCommande').find('tr');
         if (RowModif.hasClass('selected')) {
             $('.multiButton').removeAttr('disabled');
         } else {
             $('.multiButton').prop("disabled", true);
         }
      }
      checkClassMulti();
      checkClassCmd();


// Fontion qui selct l'input radion en fonction du devis selectionné : mes devis 
let checkradio = function(object){
   let statusRadio = $('.statusRadio');
  for (const key in statusRadio) {
          const element = statusRadio[key].value;
          if (element == object.devis__etat) {
              statusRadio[key].checked = true;
          } 
  }  
}

//init tout les tooltips 
$(function () {
    $('[data-toggle="tooltip"]').tooltip()
  })



    idUtilisateur = $('#idUtilisateur').val();
   
   


    // attribut classe selected: a la table mes devis 
    modifDevis.on('click','tr',function() {
        $('.multiButton').prop("disabled", true);
        $('#iframeDevis').hide();
        if ( $(this).hasClass('selected') ) {
            $(this).removeClass('selected');
        }
       else  if(modifDevis.rows().count() >= 1){
            modifDevis.$('tr.selected').removeClass('selected');
            $(this).addClass('selected');
        }
        let dataRow = modifDevis.row(this).data();
        $("#ValiderDevis").val(dataRow[0]);
        $("#VoirDevis").val(dataRow[0]);
        $("#ModifierDevis").val(dataRow[0]);
        $("#DupliquerDevis").val(dataRow[0]);
       
        $('#iframeDevis').attr('src', '');
       
        $('#loaderPdf').show();
        // requete Ajax sur le devis selectionné dans la page mes devis : 
        $.ajax({
            type: 'post',
            url: "AjaxVisio",
            data : 
            {
                "AjaxDevis" : dataRow[0]
            },
            success: function(data){
                dataSet = JSON.parse(data);
               checkradio(dataSet);
               $('#loaderPdf').hide();
               $('#iframeDevis').attr('src', 'pages/ajax/' + idUtilisateur + 'devis.pdf');
               $('#iframeDevis').show();
               $('.multiButton').removeAttr('disabled');
              

            
                 
            },
            error: function (err) {
                alert('error: ' + err);
            }

        })
     });
    
      // Attribue automatiquement la classe selected à la première ligne : 
    let selectFirst = function(){
        $('.multiButton').prop("disabled", true);
        let firstOne = $('#MyDevis').find('tr').eq(1);
        firstOne.addClass('selected');
       
        let dataRow = modifDevis.row(0).data();
        
        $("#ValiderDevis").val(dataRow[0]);
        $("#VoirDevis").val(dataRow[0]);
        $("#ModifierDevis").val(dataRow[0]);
        $("#DupliquerDevis").val(dataRow[0]);
        
        $('#iframeDevis').attr('src', '');
        $('#iframeDevis').hide();
        $('#loaderPdf').show();
        // requete Ajax sur le devis selectionné dans la page mes devis : 
        $.ajax({
            type: 'post',
            url: "AjaxVisio",
            data : 
            {
                "AjaxDevis" : dataRow[0]
            },
            success: function(data){
                
                dataSet = JSON.parse(data);
                checkradio(dataSet);
                $('#loaderPdf').hide();
                $('#iframeDevis').attr('src', 'pages/ajax/'+idUtilisateur+'devis.pdf');
                $('#iframeDevis').show();
                $('.multiButton').removeAttr('disabled');
                 
            },
            error: function (err) {
                alert('error: ' + err);
            }

        })
    }
    if ($('#MyDevis').length > 0) {
        selectFirst();
    }

    // select la prmeière ligne dans home :
    let selectFirstCMD = function(){
        $('.multiButton').prop("disabled", true);
        let firstOne = $('#MyCommande').find('tr').eq(1);
        firstOne.addClass('selected');
        
        let dataRow = validCmd.row(0).data();
        
        $("#ValideCmd").val(parseInt(dataRow[0]));
        
        $('#iframeCMD').attr('src', '');
        $('#iframeCMD').hide();
        $('#loaderCMD').show();
        // requete Ajax sur le devis selectionné dans la page mes devis : 
        $.ajax({
            type: 'post',
            url: "AjaxVisio",
            data : 
            {
                "AjaxDevis" : dataRow[0]
            },
            success: function(data){
                
                dataSet = JSON.parse(data);
                checkradio(dataSet);
                $('#loaderCMD').hide();
                $('#iframeCMD').attr('src', 'pages/ajax/devisN.pdf');
                $('#iframeCMD').show();
                $('.multiButton').removeAttr('disabled');
                 
            },
            error: function (err) {
                alert('error: ' + err);
            }

        })
    }
    if ($('#MyCommande').length > 0) {
         selectFirstCMD();
    }
   
   



      // attribut classe selected: a la table Commandes 
      $('#MyCommande').on('click','tr',function() {
        $('#iframeCMD').hide();
        $('#loaderCMD').show();
        console.log('hey');
        if ( $(this).hasClass('selected') ) {
            $(this).removeClass('selected');
        }
       else  if(validCmd.rows().count() >= 1){
        validCmd.$('tr.selected').removeClass('selected');
            $(this).addClass('selected');
        }
        let dataRow = validCmd.row(this).data();
        $("#ValideCmd").val(parseInt(dataRow[0]));
        checkClassCmd();
        $.ajax({
            type: 'post',
            url: "AjaxVisio",
            data : 
            {
                "AjaxDevis" : dataRow[0]
            },
            success: function(data){
                dataSet = JSON.parse(data);
               checkradio(dataSet);
               $('#loaderCMD').hide();
               $('#iframeCMD').attr('src', 'pages/ajax/devisN.pdf');
               $('#iframeCMD').show();    
            },
            error: function (err) {
                alert('error: ' + err);
            }

        })
     });

   
    
       // initialisation table devis : 
       let devisTable =  $('#Devis').DataTable({
            "paging": false,
            "info":   false,
            "searching": false,
            "ordering": true,
            responsive: {
                details: false
            },
            "language": {
                "decimal":        "",
                "emptyTable":     "aucuns résultats",
                "info":           "Voir _START_ to _END_ of _TOTAL_ résultats",
                "infoEmpty":      "Voir 0 to 0 of 0 résultats",
                "infoFiltered":   "(filtré dans _MAX_ total résultats)",
                "infoPostFix":    "",
                "thousands":      ",",
                "lengthMenu":     "Voir _MENU_ résultats par pages",
                "loadingRecords": "Loading...",
                "processing":     "Processing...",
                "search":         "Recherche:",
                "zeroRecords":    "Aucun résultats",
                "paginate": {
                    "first":      "Première",
                    "last":       "Dernière",
                    "next":       "Suivante",
                    "previous":   "Précédente"
                }
                  
                    
            },
            "columnDefs": [
                { "width": "40%", "targets": 2 },
                { "width": "20%", "targets": 4 },
                
                {"className": "dt-right", "targets": 6},
                {"targets": [ 1 ], className: 'dt-center'},
                {"targets": [ 2 ], className: 'dt-center'},
                {"targets": [ 3 ], className: 'dt-center'},
                {"targets": [ 4 ], className: 'dt-center'},
                {"targets": [ 5 ], className: 'dt-center'},
                {"targets": [ 6 ], className: 'dt-right'},
                {"targets": [ 7 ], "visible": false},
                {"targets": [ 0 ], "visible": false},
                { responsivePriority: 1, targets: 2 },
                { responsivePriority: 2, targets: 5 },
                { responsivePriority: 3, targets: 6 }
              ],
             
              rowReorder: {
                update: true,
                selector: 'td:first-child'
            }
           
        });


        devisTable.on( 'row-reorder', function ( e, diff, edit ) {
            for ( var i=0, ien=diff.length ; i<ien ; i++ ) {
                var rowData = devisTable.row( diff[i].node ).data();
                rowData[7].id = parseInt(diff[i].newData);
            }   

        } );
        
   


        
        // fonction select to text : devis fonction
        selectToText($('#choixDesignation'),$('#referenceS'));
        selectToText($('#UPchoixDesignation'),$('#UPreferenceS'));

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
                    let li =  $('<li></li>').text(xtendArray[index][0] + " mois " + xtendArray[index][1] + "€ H.T ")
                    .addClass('list-group-item col-4 d-flex justify-content-between align-items-center').appendTo(ul);
                    let  i =  $('<i></i>').addClass('fal fa-trash-alt btn btn-link deleteParent').val(index).appendTo(li);
                }
                 $('#xtendPrice').val("");
                 xtendCouple = [];
            }   
        })
        $("#xtendList").on('click', '.deleteParent' ,function(){
            xtendArray.splice(parseInt($(this).val()),1);
            $("#xtendList").empty();
            for (let index = 0; index < xtendArray.length; index++) {
                let ul = $("#xtendList");
                let li =  $('<li></li>').text(xtendArray[index][0] + " mois " + xtendArray[index][1] + "€ H.T ")
                .addClass('list-group-item col-4 d-flex justify-content-between align-items-center').appendTo(ul);
                let  i =  $('<i></i>').addClass('fal fa-trash-alt btn btn-link deleteParent').val(index).appendTo(li);
                
            }
        })
      
        // on check l'existance de l'objet au format jSon correspondant pour savoir si le programme exécute une modification de Devis existant  : 
        // ensuite on prérempli la datatable avec les données : 
        counter = 1 ;
        if ($('#AncienDevis').val()) {
            jsonDataAncienDevis =  JSON.parse($('#AncienDevis').val())
        if (jsonDataAncienDevis != false) {
            $('#addNewRow').removeAttr('disabled');
            for (let numberOfLines = 0; numberOfLines < jsonDataAncienDevis.length; numberOfLines++) {
                
                arrayTemp = [];
                if (jsonDataAncienDevis[numberOfLines].devl__prix_barre == 0 ) {
                    jsonDataAncienDevis[numberOfLines].devl__prix_barre = '';
                }
                for (let numberOfXtend = 0; numberOfXtend < jsonDataAncienDevis[numberOfLines].ordre.length ; numberOfXtend++) {
                     arrayCouple = [];
                     arrayCouple.push(jsonDataAncienDevis[numberOfLines].ordre[numberOfXtend].devg__type);
                     arrayCouple.push(jsonDataAncienDevis[numberOfLines].ordre[numberOfXtend].devg__prix);
                     arrayTemp.push(arrayCouple);
                }
                
               addOne(
                   devisTable,
                   jsonDataAncienDevis[numberOfLines].devl__ordre,
                   jsonDataAncienDevis[numberOfLines].devl__type,
                   jsonDataAncienDevis[numberOfLines].devl__designation,
                   jsonDataAncienDevis[numberOfLines].devl__note_client,
                   jsonDataAncienDevis[numberOfLines].devl__note_interne,
                   jsonDataAncienDevis[numberOfLines].devl__etat,
                   jsonDataAncienDevis[numberOfLines].devl__mois_garantie,
                   arrayTemp,
                   jsonDataAncienDevis[numberOfLines].devl_quantite,
                   jsonDataAncienDevis[numberOfLines].devl_puht,
                   jsonDataAncienDevis[numberOfLines].devl__prix_barre ,
                   jsonDataAncienDevis[numberOfLines].devl__modele, 
                   jsonDataAncienDevis[numberOfLines].id__fmm , 
                   jsonDataAncienDevis[numberOfLines].kw__lib ,
                   jsonDataAncienDevis[numberOfLines].prestaLib
                  

               ) 
               counter +=1 ;
               
            };
          }
          $("#referenceS").val("");
            
        }
        checkTableRows(devisTable);
        //ajout d'une ligne de devis : function location : devisFunction.js (addOne): 

        $('#addNewRow').on('click' , function(){
            $('#alertLine').addClass('invisible');
           
        })
        
        $("#addRow").on('click', function(){
           
            if ($('#choixDesignation').val() &&  $("#garantieRow").val() && $("#prixRow").val() ) {
                var selectedOption = ($("#etatRow").children("option:selected").text());
                var selectedOptionPresta = ($("#prestationChoix").children("option:selected").text());
                addOne(
                    devisTable,
                    counter,
                    $("#prestationChoix").val(),
                    $("#referenceS").val(),
                    $("#comClient").val(),
                    $("#comInterne").val(),
                    $("#etatRow").val(),
                    $("#garantieRow").val(),
                    xtendArray,
                    $("#quantiteRow").val(),
                    $("#prixRow").val(),
                    $("#barrePrice").val() ,
                    $("#choixPn").val(), 
                    $("#choixDesignation").val(), 
                    selectedOption , 
                    selectedOptionPresta
                    
                    );
                    counter +=1 ;
                    
                    
                xtendArray = [];
                    $("#prestationChoix").val(""),
                    $("#choixDesignation").val("");
                    $('#choixDesignation').selectpicker('val', '');
                    $("#referenceS").val("");
                    $("#comClient").val(""),
                    $("#comInterne").val(""),
                    $("#garantieRow").val("06"),
                    $("#quantiteRow").val("1"),
                    $("#quantiteRow").text("1"),
                    $("#prixRow").val(""),
                    $("#barrePrice").val(""),
                    $('#choixPn option').remove();
                    $('#choixPn').append(new Option('..', '' , false, true));
                    $('.selectpicker').selectpicker('refresh'); 
                    $('#choixPn').selectpicker('val', '');
                    $('#modalPresta').modal('hide');
                
            } else {
                    $('#modalPresta').modal('show');
                    $('#alertLine').removeClass('invisible');
               
                
            }
           
            });
        
        // function qui compte les lignes de la table devis et rend possible l'export :
        devisTable.on('draw.dt', function(){
            checkTableRows(devisTable);
        })

        // function efface le contact et vide la valeur de l'input :  
        $('#trash4Contact').on('click', function(){
            $('#contactSelect').val('');
            $('#contactDiv').text('Choisir un contact')
        })


        

        // disable buttons si pas de ligne:  
        let checkClass = function(){
            let RowDevis =  $('#DevisBody').find('tr');
             if (RowDevis.hasClass('selected')) {
                 $('.notActive').removeAttr('disabled');
             } else {
                 $('.notActive').prop("disabled", true);
             }
          }
        
        checkClass();
         let idRow  = false;

         // attribut classe selected: 
         devisTable.on('click','tr',function() {
            if ( $(this).hasClass('selected') ) {
                $(this).removeClass('selected');
            }
           else  if(devisTable.rows().count() >= 1){
                devisTable.$('tr.selected').removeClass('selected');
                $(this).addClass('selected');
                idRow = devisTable.row('.selected').data()[0];
            }
            checkClass();
         });
         
          // efface la ligne sur le click : 
        $('#removeLine').click( function () {
          devisTable.row('.selected').remove().draw( false );  
          checkClass();  
         }); 

         // declaration des variables a portée multiples. 
         let formContent = false;
         let UpXtendArray = false;
         let idUpdate = false ;

         // vide le formulaire d'ajout de ligne à chaque ouverture afin d'éviter les conflit en cas de fermeture sans validation : 
         $('#addNewRow').click( function (){
            $('#prestationChoix').selectpicker('val', 'VTE');
            $("#xtendList").empty();
            $("#comClient").val('');
            $("#comInterne").val('');
            $("#prixRow").val('');
            $("#barrePrice").val('');
            xtendArray = [];
         })

        // prerempli le formulaire de modification : 
         $('#modifyLine').click( function () {
            
            // vide en cas de fermeture sans modif (UPxtendList double) :
           
            $('#UPprestationChoix').selectpicker('val', 'VTE');
            $("#UPchoixDesignation").val('ZT420');
           
            $("#UPxtendList").empty();
            $("#UPcomClient").val('');
            $("#UPchoixDesignation").val('');
            $("#UPcomInterne").val('');
            $("#UPprixRow").val('');
            $("#UPbarrePrice").val('');
            UpXtendArray = [];
            dataObject =  devisTable.row('.selected').data();
            formContent = dataObject[7];
            // charge les pn correspondant à la famille 
            $.ajax({
                type: 'post',
                url: "AjaxPn",
                data : 
                {
                    "AjaxPn" : formContent.id__fmm
                },
                success: function(data){
                   
                    dataSet = JSON.parse(data);
                    $('#UPchoixPn option').remove();
                    $('#UPchoixPn').append(new Option('..', '' , false, true));
                    for (let index = 0; index < dataSet.length; index++) {
                        var opt = $("<option>").val(dataSet[index].apn__pn).text(dataSet[index].apn__pn);
                        $('#UPchoixPn').append(new Option(dataSet[index].apn__pn_long + " " + dataSet[index].apn__desc_short ,dataSet[index].apn__pn));
                        
                    }
                    
                    $('.selectpicker').selectpicker('refresh'); 
                    $('#UPchoixPn').selectpicker('val', formContent.pn);
                    
                    
    
                },
                error: function (err) {
                    alert('error: ' + err);
                }
    
            })
            
           $("#UPprestationChoix").val(formContent.prestation);
           $("#UPreferenceS").val(formContent.designation);
           $("#UPcomClient").val(formContent.comClient);
           $("#UPchoixDesignation").selectpicker('val', formContent.id__fmm);
          
                   
           $("#UPcomInterne").val(formContent.comInterne);
           $("#UPquantiteRow").val(formContent.quantite);

          console.log(formContent.garantie);
           $('#UPgarantieRow').selectpicker('val' ,formContent.garantie);
           $('#UPetatRow').selectpicker('val', formContent.etat);
           
           $("#UPbarrePrice").val(formContent.prixBarre);
           
           $("#UPprixRow").val(formContent.prix);
           $('#UPreferenceS').val(formContent.designation);
           
           UpXtendArray = formContent.xtend;
           idUpdate = formContent.id;
           if (UpXtendArray.length > 0) {
            for (let index = 0; index < UpXtendArray.length; index++) {
                let ul = $("#UPxtendList");
                let li =  $('<li></li>').text(UpXtendArray[index][0] + " mois " + UpXtendArray[index][1] + "€ H.T ")
                .addClass('list-group-item col-4 d-flex justify-content-between align-items-center').appendTo(ul);
                let  i =  $('<i></i>').addClass('fal fa-trash-alt btn btn-link deleteParent').val(index).appendTo(li); 
            }
           }
           checkClass();   
         }); 







        //extensions de garanties dans le formulaire de mofification : 
         let UpXtendMois ; 
         let UpXtendPrix;
         $("#UPxtendGr").on('click', function(){    
             if (!$("#UPxtendPrice").val() && !$("#UPxtendPrice").val()) {
                 $("#UPxtendPrice").addClass('alert alert-danger');
             }else {
                 $("#UPxtendPrice").removeClass('alert alert-danger');
                 $("#UPxtendList").empty();
                 UpXtendPrix = $('#UPxtendPrice').val();  
                 UpXtendMois = $('#UPxtendMois').val(); 
                 let  UpXtendCouple = [ UpXtendMois , UpXtendPrix ]; 
                 UpXtendArray.push(UpXtendCouple);
                 for (let index = 0; index < UpXtendArray.length; index++) {
                     let ul = $("#UPxtendList");
                     let li =  $('<li></li>').text(UpXtendArray[index][0] + " mois " + UpXtendArray[index][1] + "€ H.T ")
                     .addClass('list-group-item col-4 d-flex justify-content-between align-items-center').appendTo(ul);
                     let  i =  $('<i></i>').addClass('fal fa-trash-alt btn btn-link deleteParent').val(index).appendTo(li);
                    
                 }
                 $('#UPxtendPrice').val("");
                 UpXtendCouple = [];
             }   
         })
         $("#UPxtendList").on('click', '.deleteParent' ,function(){
             UpXtendArray.splice(parseInt($(this).val()),1);
             $("#UPxtendList").empty();
             for (let index = 0; index < UpXtendArray.length; index++) {
                 let ul = $("#UPxtendList");
                 let li =  $('<li></li>').text(UpXtendArray[index][0] + " mois " + UpXtendArray[index][1] + "€ H.T ")
                 .addClass('list-group-item col-4 d-flex justify-content-between align-items-center').appendTo(ul);
                 let  i =  $('<i></i>').addClass('fal fa-trash-alt btn btn-link deleteParent').val(index).appendTo(li);
             }
         })

         // fait disparaitre l'alerte : 
         $('#modifyLine').on('click' , function(){
            $('#alertLineModif').addClass('invisible');
        })
         // suprime la ligne selctionnee et la remplace par cette meme ligne modifie avec id identique : 
         $("#updateRow").on('click', function(){
            var selectedOption = ($("#UPetatRow").children("option:selected").text());
            var selectedOptionPresta = ($("#UPprestationChoix").children("option:selected").text());
            if ($('#UPchoixDesignation').val() &&  $("#UPgarantieRow").val() && $("#UPprixRow").val()) {
                modifyLine(
                    devisTable,
                    idUpdate,
                    $("#UPprestationChoix").val(),
                    $("#UPreferenceS").val(),
                    $("#UPcomClient").val(),
                    $("#UPcomInterne").val(),
                    $("#UPetatRow").val(),
                    $("#UPgarantieRow").val(),
                    UpXtendArray,
                    $("#UPquantiteRow").val(),
                    $("#UPprixRow").val(),
                    $("#UPbarrePrice").val() ,
                    $("#UPchoixPn").val(),
                    $("#UPchoixDesignation").val(),
                    selectedOption , 
                    selectedOptionPresta
                    
                ) 
                checkClass();  
                $("#UPxtendList").empty();
                UpXtendArray = [];
                $('#modalModif').modal('hide');
                
            } else {
                $('#modalModif').modal('show');
                $('#alertLineModif').removeClass('invisible');
                
            }
           

        })



        // envoi au module de traitement PDF : 
        $('#xPortData').click(function() {
            let rowData =  devisTable.cells('',7).data();
            
            let arrayOfDevis = $.map(rowData, function(value) {
                return [value];
            });
            let paramJSON = JSON.stringify(arrayOfDevis);
            $("#dataDevis").val(paramJSON);
            $("#devisSend").submit();
        });
       

        cmdArray = [];
        // fonction de toogle des input radio : 
        $(".radioCmd").on('click', function(){
            $(this).parents('.border').removeClass('border-danger');
            let borderRed =  $('.remove-border');
            if (borderRed.hasClass('border-danger')  ) {
                $('#SendCmd').prop("disabled", true);
            }else{
                $('#SendCmd').removeAttr('disabled');
            }
            cmdArray.push(this.value);
            
        })

        // fonction qui check si extensions de garanties absentes : 
        let checkXtend = function(){
        let borderCount = $('.remove-border').length;
        if (borderCount > 0 ) { 
            $('#SendCmd').prop("disabled", true);
        }else{
            $('#SendCmd').removeAttr('disabled');
        }
        }
        checkXtend();


         


        // fonction de validation de commandes : 
        $('#SendCmd').on('click', function(){
            radio = $(".radioCmd");
            radioP = $('.radioCmdP');
            arrayOFsheet = [];
            arrayOFcom = [];
            
            for (let nb = 0; nb < radio.length; nb++) {
                if(radio[nb].checked == true){
                    arrayTemp = [];
                    arrayTemp.push(radio[nb].value,radioP[nb].value);
                    arrayOFsheet.push(arrayTemp);    
                }
            }
            commentaireLigne = $('.CMD-COM');
            for (let nb2 = 0; nb2 < commentaireLigne.length; nb2++) {
                arrayOFcom.push(commentaireLigne[nb2].value);
            }

         arrayOfItem = JSON.parse($('#arrayOfLines').val());
          for (let index = 0; index < arrayOfItem.length; index++) {
                
                if (arrayOFsheet[index] === undefined) {
                    arrayOFsheet[index] = [null,null];
                }
               arrayOfItem[index].devl__prix_barre = arrayOFsheet[index];
               arrayOfItem[index].devl__note_interne = arrayOFcom[index];
          }
          $('#arrayLigneDeCommande').val(JSON.stringify(arrayOfItem));
          $('#ComInterCommande').val($('#cmdInterneNote').val());
          $('#formValideCMD').submit();
        })



        //masques le elements à l'ouverture de la page : 
        let hideElement = function(){
            $('#navLivraison').hide();
            $('#navDevis').hide();
            $('#navLivraison').removeClass("d-none");
            $('#navDevis').removeClass("d-none");
        }
        
        hideElement();

        //navigation menu du nouveau devis :
        Nav($("#toogleSociete") , $('#navSociete'), $('#navLivraison'), $('#navDevis'));
        Nav($("#toogleLivraison") , $('#navLivraison'), $('#navSociete'), $('#navDevis'));
        Nav($("#toogleDevis") , $('#navDevis'), $('#navSociete'),  $('#navLivraison'));

        $(".selectpicker").selectpicker({
            noneSelectedText : '...' 
        });


        //function de selection de l'affichage PN : 
        $('#choixDesignation').on('change', function(){
            var selectedOption = parseInt($(this).children("option:selected").val());
            $.ajax({
                type: 'post',
                url: "AjaxPn",
                data : 
                {
                    "AjaxPn" : selectedOption
                },
                success: function(data){
                    dataSet = JSON.parse(data);
                    $('#choixPn option').remove();
                    $('#choixPn').append(new Option('..', '' , false, true));
                    for (let index = 0; index < dataSet.length; index++) {
                     
                        $('#choixPn').append(new Option(dataSet[index].apn__pn_long + " " + dataSet[index].apn__desc_short ,dataSet[index].apn__pn));
                        
                    }
                    $('.selectpicker').selectpicker('refresh'); 
    
                },
                error: function (err) {
                    alert('error: ' + err);
                }
    
            })
            
           
        });

        

        $('#UPchoixDesignation').on('change', function(){
            
            var selectedOption = parseInt($(this).children("option:selected").val());
            $.ajax({
                type: 'post',
                url: "AjaxPn",
                data : 
                {
                    "AjaxPn" : selectedOption
                },
                success: function(data){
                   
                    dataSet = JSON.parse(data);
                    $('#UPchoixPn option').remove();
                    $('#UPchoixPn').append(new Option('..', '' , false, true));
                    for (let index = 0; index < dataSet.length; index++) {
                       
                        $('#UPchoixPn').append(new Option(dataSet[index].apn__pn_long + " " + dataSet[index].apn__desc_short ,dataSet[index].apn__pn));
                        
                    }
                    $('.selectpicker').selectpicker('refresh'); 
    
                },
                error: function (err) {
                    alert('error: ' + err);
                }
    
            })
            
           
        });



        //init des editeur de texte mdb : 

        $("#globalComClient").mdbWYSIWYG({
        });

        $("#globalComInt").mdbWYSIWYG({
        });
        

        $("#comClient").mdbWYSIWYG({ 
         });

         $("#UPcomClient").mdbWYSIWYG({ 
        });
        

         $("#comInterne").mdbWYSIWYG({ 
        });

        $("#UPcomInterne").mdbWYSIWYG({ 
        });


       

        
    } );