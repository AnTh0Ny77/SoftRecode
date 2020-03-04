
$(document).ready(function() {
    
let tableClient;
let tableLivraison;

//appel ajax au click table client : 
    $('#AjaxClient').on('click', function(){
        let dataSet = [];
        $.ajax({
            type: 'post',
            url: "AjaxSociete",
            data : 
            {
                "AjaxSociete" : 7
            },
            success: function(data){
                dataSet = JSON.parse(data);
                $('#modalClient').modal('show');
                //initialisation de la table Client : 
                 tableClient = $('#client').DataTable({
                     data: dataSet,
                     "columns": [
                        { "data": "client__id" },
                        { "data": "client__societe" },
                        { "data": "client__ville" }  
                    ],
                    "paging": true,
                    "info":   true,
                    retrieve: true,
                    "deferRender": true,
                    "searching": true,      
                    });
                // fonction selection du client  : 
                $('#client tbody').on('click', 'tr', function () {
                let donne = tableClient.row( this ).data();
                $("#choixClient").val(donne.client__id);
                $("#formSelectClient").submit();
                });    
            },
            error: function (err) {
                alert('error: ' + err);
            }

        })
    })

    //appel ajax au click table livraison : 
    $('#buttonLivraison').on('click', function(){
        let dataSet = [];
        $.ajax({
            type: 'post',
            url: "AjaxSociete",
            data : 
            {
                "AjaxLivraison" : 7
            },
            success: function(data){
                dataSet = JSON.parse(data);
                $('#ModalLivraison').modal('show');
                //initialisation de la table Client : 
                tableLivraison = $('#Livraison').DataTable({
                     data: dataSet,
                     "columns": [
                        { "data": "client__id" },
                        { "data": "client__societe" },
                        { "data": "client__ville" }  
                    ],
                    "paging": true,
                    "info":   true,
                    retrieve: true,
                    "deferRender": true,
                    "searching": true,      
                    });
                 // fonction selection de l'adresse de livraison  : 
                $('#Livraison tbody').on('click', 'tr', function () {
                let donne = tableLivraison.row( this ).data();
                $("#choixLivraison").val(donne.client__id);
                $("#formSelectLivraison").submit();
              });
            },
            error: function (err) {
                alert('error: ' + err);
            }
        })
    })
 

    // initi table mesDevis : 
    let modifDevis = $('#MyDevis').DataTable({
        "paging": true,
         "info":   false,
        retrieve: true,
        "deferRender": true,
        "searching": false,  
       
    })
    // ini table commandes :
    let validCmd = $('#MyCommande').DataTable({
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

    // attribut classe selected: a la table mes devis 
    modifDevis.on('click','tr',function() {
        console.log("hey");
        if ( $(this).hasClass('selected') ) {
            $(this).removeClass('selected');
        }
       else  if(modifDevis.rows().count() >= 1){
            modifDevis.$('tr.selected').removeClass('selected');
            $(this).addClass('selected');
        }
        let dataRow = modifDevis.row(this).data();
        $("#ValiderDevis").val(dataRow[0]);
        $("#RefuserDevis").val(dataRow[0]);
        $("#VoirDevis").val(dataRow[0]);
        $("#ModifierDevis").val(dataRow[0]);
        $("#DupliquerDevis").val(dataRow[0]);
        checkClassMulti();
     });

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

       // initialisation table contact  
        let tableContact = $("#contactTable").DataTable({
            "paging": false,
            "info":   true,
            "deferRender": true,
            retrieve: true,
            deferRender: true,
            "searching": false, 
        })

            
       

        
    
        // fonction selection du contact : 
        $('#contactTable tbody').on('click','tr', function(){
            let text = tableContact.row( this ).data();
            $("#choixContact").val(text[0]);
            $("#formSelectContact").submit();
        })

       
      
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
            for (let numberOfLines = 0; numberOfLines < jsonDataAncienDevis.length; numberOfLines++) {
                arrayTemp = [];
                if (jsonDataAncienDevis[numberOfLines].devl__prix_barre == '0') {
                    jsonDataAncienDevis[numberOfLines].devl__prix_barre = false;
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
            });
        
           
        
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
            $("#prestationChoix").val('..');
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
            $("#UPprestationChoix").val('..');
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

            console.log(arrayOfDevis);
            
            let paramJSON = JSON.stringify(arrayOfDevis);

            console.log(paramJSON);
            $("#dataDevis").val(paramJSON);
          
            
        });
       
        //reload page : 
        $("#xPortData").click(function(){
            $("#ValidDevis").val(202);
            $("#DevisValidForm").submit();
        });


        cmdArray = [];
        // fonction de toogle des input radio : 
        $(".radioCmd").on('click', function(){
            $(this).parents('.border').removeClass('border-danger');
            let borderRed =  $('.remove-border');
            if (borderRed.hasClass('border-danger')) {
                $('#SendCmd').prop("disabled", true);
            }else{
                $('#SendCmd').removeAttr('disabled');
            }
            cmdArray.push(this.value);
            
        })

        // fonction de validation de commandes : 
        $('#SendCmd').on('click', function(){
            radio = $(".radioCmd");
            for (let nb = 0; nb < radio.length; nb++) {
                if(radio[nb].checked == true){
                    console.log(radio[nb].value);
                }
            }



         arrayOfItem = JSON.parse($('#arrayOfLines').val());
          for (let index = 0; index < arrayOfItem.length; index++) {
              let  element = arrayOfItem[index].devl__prix_barre;
              console.log(element);
          }
          
           
          
          
         
        })
        

    } );