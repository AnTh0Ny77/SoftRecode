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
            "columnDefs": [
                { "width": "40%", "targets": 2 },
                { "width": "20%", "targets": 4 },
                {"className": "dt-center", "targets": "_all"},
                {"targets": [ 7 ], "visible": false}
              ],
            rowReorder: true,
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
        $('#choixDesignation option').on('click', function(){
            $('#referenceS').val($(this).text());
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
                console.log(xtendArray);
            }
        })
           
        //ajout d'une ligne de devis :
        counter = 1 ;
        $("#addRow").on('click', function(){
            let row = []; 
            row.push(counter);
            let prestation = $("#prestationChoix").val();
            row.push(prestation);
            let designation = $("#referenceS").val();
            let comClient = $("#comClient").val();
            let comInterne = $("#comInterne").val();
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
            let etat = $("#etatRow").val();
            row.push(etat);
            let garantie = $("#garantieRow").val() + " mois ";
            if (xtendArray.length > 0) {
                let element;
                let xtendString = "";
                for (let index = 0; index < xtendArray.length; index++) {
                    element  =  xtendArray[index][0] + ' mois ' + + xtendArray[index][1] + ' €<br>';
                    xtendString += element;
                }
                row.push( garantie + " <hr> <b>Extensions</b> : <br>" + xtendString);    
            } else {
                row.push( garantie);
            }
            let quantite =  $("#quantiteRow").val()  ;
            row.push( quantite);
            let prix = $("#prixRow").val();
            let prixBarre = $("#barrePrice").val()
            let prixMultiple ;
            if (prixBarre.length > 0) {
                prixMultiple =  ' <s>' + prixBarre  + "€</s> " + prix + " €" ;
            }else {prixMultiple =  $("#prixRow").val() + " €" ;};
            row.push(prixMultiple);

            let rowObject = new Object();
            rowObject.prestation = prestation;
            rowObject.designation = designation;
            rowObject.comClient = comClient;
            rowObject.comInterne = comInterne;
            rowObject.etat = etat;
            rowObject.garantie = $("#garantieRow").val();
            rowObject.xtend = xtendArray;
            rowObject.quantite = quantite;
            rowObject.prix = prix;
            rowObject.prixBarre = prixBarre;
            row.push(rowObject);
            devisTable.row.add(row).draw( false );
            row = [];
            xtendArray = [];
            counter ++; 
        })

        // disable buttons si pas de ligne:  
        let checkClass = function(){
            let test =  $('#DevisBody').find('tr');
             if (test.hasClass('selected')) {
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


         // prerempli le formulaire de modification : 
         $('#modifyLine').click( function () {
           let dataObject =  devisTable.row('.selected').data();
           let formContent = dataObject[7];
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
           checkClass();   
         }); 
        

    } );