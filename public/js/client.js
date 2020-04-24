
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
                console.log(dataSet)
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
    {"data": "keyword__lib"}],  
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
    {"data": "keyword__lib"}],  
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
        "order": [[ 2, "asc" ] , [3 , "desc"]],
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


    // attribut classe selected: a la table mes devis 
    modifDevis.on('click','tr',function() {
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
        checkClassMulti();
        // requete Ajax sur le devis selectionné dans la page mes devis : 
        $.ajax({
            type: 'post',
            url: "AjaxDevis",
            data : 
            {
                "AjaxDevis" : dataRow[0]
            },
            success: function(data){
                dataSet = JSON.parse(data);
                checkradio(dataSet[0]);
                $('#AjaxId').text(dataSet[0].devis__id);
                $('#AjaxSociete').html(dataSet[0].client__societe + "<br>" + dataSet[0].client__ville + " " + dataSet[0].client__cp );
                if (dataSet[0].contact__nom) {
                    $('#AjaxContact').html(dataSet[0].contact__nom + " " + dataSet[0].contact__prenom );
                }else {  $('#AjaxContact').html('...') }
                if (dataSet[0].client__livraison_societe) {
                    $('#AjaxLivraison').html(dataSet[0].client__livraison__adr1 + "<br>" + dataSet[0].client__livraison_ville + " " + dataSet[0].client__livraison_cp);
                }else{ $('#AjaxLivraison').html(dataSet[0].client__adr1 + "<br>" + dataSet[0].client__ville + " " + dataSet[0].client__cp ) }
                $('#AjaxEtat').text(dataSet[0].keyword__lib);
                $('#AjaxPort').html(dataSet[0].devis__port + ' €' ) ;
                let listOfItem = $('#listOfAjax');
                listOfItem.html(' ');
                let array = dataSet[1];
                for (let index = 0; index < array.length ; index++) {
                   let li = document.createElement('li');
                   let content = document.createTextNode( array[index].devl_quantite + " x " +  array[index].devl__designation + ' : ' + array[index].devl_puht + " €" );
                   li.appendChild(content);
                   listOfItem.append(li);
                   listOfItem.children('li').addClass('list-group-item text-white bg-secondary font-weight-bold');
                    
                }
                 
            },
            error: function (err) {
                alert('error: ' + err);
            }

        })
     });

      // Attribue automatiquement la classe selected à la première ligne : 
    let selectFirst = function(){
        let firstOne = $('#MyDevis').find('tr').eq(1);
        firstOne.addClass('selected');
        
        let dataRow = modifDevis.row(0).data();
        
        $("#ValiderDevis").val(dataRow[0]);
        $("#VoirDevis").val(dataRow[0]);
        $("#ModifierDevis").val(dataRow[0]);
        $("#DupliquerDevis").val(dataRow[0]);
        checkClassMulti();
        // requete Ajax sur le devis selectionné dans la page mes devis : 
        $.ajax({
            type: 'post',
            url: "AjaxDevis",
            data : 
            {
                "AjaxDevis" : dataRow[0]
            },
            success: function(data){
                
                dataSet = JSON.parse(data);
                checkradio(dataSet[0]);
                $('#AjaxId').text(dataSet[0].devis__id);
                $('#AjaxSociete').html(dataSet[0].client__societe + "<br>" + dataSet[0].client__ville + " " + dataSet[0].client__cp );
                if (dataSet[0].contact__nom) {
                    $('#AjaxContact').html(dataSet[0].contact__nom + " " + dataSet[0].contact__prenom );
                }else {  $('#AjaxContact').html('...') }
                if (dataSet[0].client__livraison_societe) {
                    $('#AjaxLivraison').html(dataSet[0].client__livraison__adr1 + "<br>" + dataSet[0].client__livraison_ville + " " + dataSet[0].client__livraison_cp);
                }else{ $('#AjaxLivraison').html(dataSet[0].client__adr1 + "<br>" + dataSet[0].client__ville + " " + dataSet[0].client__cp ) }
                $('#AjaxEtat').text(dataSet[0].keyword__lib);
                $('#AjaxPort').html(dataSet[0].devis__port + ' €' ) ;
                let listOfItem = $('#listOfAjax');
                listOfItem.html(' ');
                let array = dataSet[1];
                for (let index = 0; index < array.length ; index++) {
                   let li = document.createElement('li');
                   let content = document.createTextNode( array[index].devl_quantite + " x " +  array[index].devl__designation + ' : ' + array[index].devl_puht + " €" );
                   li.appendChild(content);
                   listOfItem.append(li);
                   listOfItem.children('li').addClass('list-group-item text-white bg-secondary font-weight-bold');
                    
                }
                 
            },
            error: function (err) {
                alert('error: ' + err);
            }

        })
    }
    if ($('#MyDevis').length > 0) {
        selectFirst();
    }
   

      // attribut classe selected: a la table Commandes 
      validCmd.on('click','tr',function() {
        
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
     });

   
    
       // initialisation table devis : 
       let devisTable =  $('#Devis').DataTable({
            "paging": false,
            "info":   false,
            "searching": false,
            "ordering": false,
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
                {"className": "dt-center", "targets": "_all"},
                {"targets": [ 7 ], "visible": false},
                {"targets": [ 0 ], "visible": false},
                { responsivePriority: 1, targets: 2 },
                { responsivePriority: 2, targets: 5 },
                { responsivePriority: 3, targets: 6 }
              ],
             
              rowReorder: {
                update: false,
                selector: 'td:first-child'
            }
           
        });


        // Programme d'ajout de ligne dans le devis : 
        //traitement du formulaire : 
        $('#choixDesignation').on('change', function(){
            var selectedOption = $(this).children("option:selected").text();
            $('#referenceS').val(selectedOption);
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
                if (jsonDataAncienDevis[numberOfLines].devl__prix_barre == '0') {
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
                   counter,
                   jsonDataAncienDevis[numberOfLines].devl__type,
                   jsonDataAncienDevis[numberOfLines].devl__modele,
                   jsonDataAncienDevis[numberOfLines].devl__note_client,
                   jsonDataAncienDevis[numberOfLines].devl__note_interne,
                   jsonDataAncienDevis[numberOfLines].devl__etat,
                   jsonDataAncienDevis[numberOfLines].devl__mois_garantie,
                   arrayTemp,
                   jsonDataAncienDevis[numberOfLines].devl_quantite,
                   jsonDataAncienDevis[numberOfLines].devl_puht,
                   jsonDataAncienDevis[numberOfLines].devl__prix_barre
               ) 
            };
          }
            
        }
        checkTableRows(devisTable);
        //ajout d'une ligne de devis : function location : devisFunction.js (addOne): 
        
        $("#addRow").on('click', function(){
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
                $("#barrePrice").val()
                );
            xtendArray = [];
                $("#prestationChoix").val(""),
                $("#choixDesignation").val("");
                $("#referenceS").val("");
                $("#comClient").val(""),
                $("#comInterne").val(""),
                $("#etatRow").val(""),
                $("#garantieRow").val(""),
                $("#quantiteRow").val("1"),
                $("#quantiteRow").text("1"),
                $("#prixRow").val(""),
                $("#barrePrice").val("")
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
            $("#prestationChoix").val('Vente');
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
            $("#UPprestationChoix").val('Vente');
            $("#UPxtendList").empty();
            $("#UPcomClient").val('');
            $("#UPcomInterne").val('');
            $("#UPprixRow").val('');
            $("#UPbarrePrice").val('');
            UpXtendArray = [];
            dataObject =  devisTable.row('.selected').data();
            formContent = dataObject[7];
           $("#UPprestationChoix").val(formContent.prestation);
           $("#UPreferenceS").val(formContent.designation);
           $("#UPcomClient").val(formContent.comClient);
           $("#UPcomInterne").val(formContent.comInterne);
           $("#UPquantiteRow").val(formContent.quantite);
           $("#UPgarantieRow").val(formContent.garantie);
           $("#UPbarrePrice").val(formContent.prixBarre);
           $("#UPetatRow").val(formContent.etat);
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

         // suprime la ligne selctionnee et la remplace par cette meme ligne modifie avec id identique : 
         $("#updateRow").on('click', function(){
            let row = []; 
            row.push(idUpdate);
            let prestation = $("#UPprestationChoix").val();
            row.push(prestation);
            let designation = $("#UPreferenceS").val();
            let comClient = $("#UPcomClient").val();
            let comInterne = $("#UPcomInterne").val();
            if ( comClient.length > 0 && comInterne.length > 0 ) {
                row.push(designation + "<br>  <hr>" + '<b>Commentaire : </b>' + comClient  + '<br> <b>Commentaire interne</b> : ' + comInterne )
            } 
            else if(comClient.length > 0 && comInterne.length < 1 ){
                row.push(designation + "<br>  <hr>" + '<b>Commentaire : </b>' + comClient);
            }
            else if(comInterne.length > 0 && comClient.length < 1 ){
                row.push(designation + "<br> <hr>" + '<b>Commentaire interne</b> :' + comInterne);
            }
            else {
                row.push(designation);
            }
            let etat = $("#UPetatRow").val();
            row.push(etat);
            let garantie = $("#UPgarantieRow").val() + " mois ";
            if (UpXtendArray.length > 0) {
                let element;
                let UpXtendString = "";
                for (let index = 0; index < UpXtendArray.length; index++) {
                    element  =  UpXtendArray[index][0] + ' mois ' + + UpXtendArray[index][1] + ' €<br>';
                    UpXtendString += element;
                }
                row.push( garantie + " <hr> <b>Extensions</b> : <br>" + UpXtendString);    
            } else {
                row.push( garantie);
            }
            let quantite =  $("#UPquantiteRow").val()  ;
            row.push( quantite);
            let prix = $("#UPprixRow").val();
            let prixBarre = $("#UPbarrePrice").val()
            let prixMultiple ;
            if (prixBarre.length > 0) {
                prixMultiple =  ' <s>' + prixBarre  + "€</s> " + prix + " €" ;
            }else {prixMultiple =  $("#UPprixRow").val() + " €" ;};
            row.push(prixMultiple);

            let rowObject = new Object();
            rowObject.id = idUpdate;
            rowObject.prestation = prestation;
            rowObject.designation = designation;
            rowObject.comClient = comClient;
            rowObject.comInterne = comInterne;
            rowObject.etat = etat;
            rowObject.garantie = $("#UPgarantieRow").val();
            rowObject.xtend = UpXtendArray;
            rowObject.quantite = quantite;
            rowObject.prix = prix;
            rowObject.prixBarre = prixBarre;
            row.push(rowObject);
            devisTable.row('.selected').data( row ).draw( false );  
            checkClass();  
            $("#UPxtendList").empty();
            row = [];
            UpXtendArray = [];
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


        
    } );