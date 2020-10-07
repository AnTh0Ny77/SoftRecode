$(document).ready(function() {

//initialization de  tout les tooltips 
$(function () {
    $('[data-toggle="tooltip"]').tooltip()
})

  let factureT = $("#factureTable").DataTable({language:{decimal:"",emptyTable:"aucuns résultats",info:"Voir _START_ to _END_ of _TOTAL_ résultats",infoEmpty:"Voir 0 to 0 of 0 résultats",infoFiltered:"(filtré dans _MAX_ total résultats)",infoPostFix:"",thousands:",",lengthMenu:"Voir _MENU_ résultats par pages",loadingRecords:"Loading...",processing:"Processing...",search:"Recherche:",zeroRecords:"Aucun résultats",paginate:{first:"Première",last:"Dernière",next:"Suivante",previous:"Précédente"}},columnDefs:[{targets:[1],visible:!1},{targets:[0],visible:!1}],order:[[1,"desc"]],paging:!0,info:!1,pageLength:50,retrieve:!0,deferRender:!0,searching:!1});

  idUtilisateur = $('#idUtilisateur').val();


//init de l'éditeur de text: 
ClassicEditor
.create( document.querySelector( '#comTVA' ) , 
    {
    fontColor: 
    {
        colors: 
        [
            {
            color: 'black',
            label: 'Black'
            },
            {
            color: 'red',
            label: 'Red'
            },
            {
            color: 'DarkGreen',
            label: 'Green'
            },
            {
            color: 'Gold',
            label: 'Yellow'
            },
            {
            color: 'Blue',
            label: 'Blue',
            },
            ]
            },
            toolbar: 
            [ 'heading', '|',  'bold', 'italic', 'bulletedList', 'numberedList' , 'link', '|', 'undo' , 'redo' ,  'fontColor']
            })
            .then( newEditor => 
            {
            ckComClient = newEditor;
            })
            .catch( error => 
            {
            console.error( error );
            });   

            


            ClassicEditor
            .create( document.querySelector( '#comClientLigne' ) , 
            {
            fontColor: 
            {
            colors: 
            [
                {
                color: 'black',
                label: 'Black'
                },
                {
                color: 'red',
                label: 'Red'
                },
                {
                color: 'DarkGreen',
                label: 'Green'
                },
                {
                color: 'Gold',
                label: 'Yellow'
                },
                {
                color: 'Blue',
                label: 'Blue',
                },
                ]
                },
                toolbar: 
                [ 'heading', '|',  'bold', 'italic', 'bulletedList', 'numberedList' , 'link', '|', 'undo' , 'redo' ,'fontColor']
                })
                .then( newEditor => 
                {
                ckComLigne = newEditor;
                })
                .catch( error => 
                {
                console.error( error );
                });   


                
            ClassicEditor
                .create( document.querySelector( '#comFacture' ) , 
                {
                fontColor: 
                {
                colors: 
                [
                    {
                    color: 'black',
                    label: 'Black'
                    },
                    {
                    color: 'red',
                    label: 'Red'
                    },
                    {
                    color: 'DarkGreen',
                    label: 'Green'
                    },
                    {
                    color: 'Gold',
                    label: 'Yellow'
                    },
                    {
                    color: 'Blue',
                    label: 'Blue',
                    },
                    ]
                    },
                    toolbar: 
                    [ 'heading', '|',  'bold', 'italic', 'bulletedList', 'numberedList' , 'link', '|', 'undo' , 'redo' , 'fontColor']
                    })
                    .then( newEditor => 
                    {
                    ckComFacture = newEditor;
                    })
                    .catch( error => 
                    {
                    console.error( error );
                    });   
    


  // recupere la data pour alimenter la table societe:
  arrayClient = [];
  
    $.ajax({
        type: 'post',
        url: "AjaxSociete",
        data : {"AjaxSociete" : 7},    
    success: function(data){
        dataSet = JSON.parse(data);
        arrayClient = dataSet;
        //initialization de la datatable des societe
        tableClient=$("#factureClient").DataTable(
            {language:{decimal:"",emptyTable:"aucuns résultats",info:"Voir _START_ a _END_ de _TOTAL_ résultats",infoEmpty:"Voir 0 to 0 of 0 résultats",infoFiltered:"(filtré dans _MAX_ total résultats)",infoPostFix:"",thousands:",",lengthMenu:"Voir _MENU_ résultats par pages",loadingRecords:"Loading...",processing:"Processing...",search:"Recherche:",zeroRecords:"Aucun résultats",paginate:{first:"Première",last:"Dernière",next:"Suivante",previous:"Précédente"}}
            ,paging:!0,info:!0,retrieve:!0,deferRender:!0,searching:!0
            , data: arrayClient,
            columns: 
            [
                {data:"client__id"},
                {data:"client__societe"},
                {data:"client__cp"},
                {data:"client__ville"}
            ],
            "lengthMenu": [ 8]
            });

             //attribut de la classe selected à la table de selection client dans le modal:

             tableClient.on('dblclick' , 'tr' , function()
             {
                //classe selected
                if ($(this).hasClass('selected')) 
                {
                    $(this).removeClass('selected');
                }
                else if (tableClient.rows().count() >= 1) 
                {
                    tableClient.$('tr.selected').removeClass('selected');
                    $(this).addClass('selected');
                }

                //requete et remplissage du select-pricker et du  form
                let dataRow = tableClient.row(this).data();
                dataFiche = dataRow.client__id;
                $('#selectContact option').remove();
                $.ajax({
                    type: 'post',
                    url: "AjaxClientContact",
                    data:
                    {
                        "AjaxDevis": dataFiche
                    },
                    success: function (data) 
                    {
                        dataSet = JSON.parse(data);
                        $('#clientName').text(dataSet[0].client__societe)
                        $('#textClient').html('<b>'+ dataSet[0].client__adr1 + ' ' + dataSet[0].client__adr2  +
                        '<br> '+ dataSet[0].client__cp+ ' '+ dataSet[0].client__ville + '</b>');
                        let clientList = dataSet[1];
                        $('#postSociety').val(dataSet[0].client__id);
                        $('#selectContact').append(new Option( "Aucun Contact" ,""));
                        for (let index = 0; index < clientList.length; index++) 
                        {
                            $('#selectContact').append(new Option(clientList[index].contact__nom + " " + 
                            clientList[index].contact__prenom + ' ' +  clientList[index].contact__prenom  + " " +
                             clientList[index].kw__lib , clientList[index].contact__id ));   
                        }
                        $('.selectpicker').selectpicker('refresh'); 
                        if (dataSet[0].devis__contact__id > 1 ) 
                        {
                            $('#selectContact').selectpicker('val',dataSet[0].devis__contact__id);
                        } else  $('#selectContact').selectpicker('val', "");
                           
                    },
                    error: function (err) {
                        console.log('error: ' , err);
                    }

                })

            })
        
        },

    error: function (err) {
    console.log('error: ' + err);}
    })


   



   // attribut classe selected: a la table fiche de travail sur le click:
   factureT.on('click', 'tr', function () {
        
    $('#iframeFacture').hide();
    if ($(this).hasClass('selected')) {
        $(this).removeClass('selected');
    }
    else if (factureT.rows().count() >= 1) {
        factureT.$('tr.selected').removeClass('selected');
        $(this).addClass('selected');
    }
    let dataRow =factureT.row(this).data();
    $('#iframeFacture').attr('src', '');
    $('#loaderFacture').show();
    // requete Ajax 
    $.ajax({
        type: 'post',
        url: "factureVisio",
        data:
        {
            "AjaxFT": dataRow[0]
        },
        success: function (data) 
        {
            dataSet = JSON.parse(data);

            $('#loaderFacture').hide();
            $('#iframeFacture').html(dataSet);
            $('#iframeFacture').show();

            //click pour chaque ligne
            $('.clickFact').on('click',  function(){

                dataFicheLigne = this.value;
               
                $.ajax({
                    type: 'post',
                    url: "AjaxLigneFT",
                    data:
                    {
                        "AjaxLigneFT": dataFicheLigne
                    },
                    success: function (data) {
                        dataSet = JSON.parse(data);
                        console.log(dataSet)
                        $('#titreLigne').text(dataSet.famille__lib+ " " + dataSet.modele + " "  + dataSet.marque);
                        $('#idCMDL').val(dataSet.devl__id);
                        $('#qteCMD').val(dataSet.devl_quantite);
                        $('#qteLVR').val(dataSet.cmdl__qte_livr);
                        $('#designationLigne').val(dataSet.devl__designation);
                        if (dataSet.cmdl__note_facture != null) 
                        {
                            ckComFacture.setData(dataSet.cmdl__note_facture);
                        }
                        if (dataSet.cmdl__qte_fact) 
                        {
                            $('#qteFTC').val(dataSet.cmdl__qte_fact);
                        } else  $('#qteFTC').val(dataSet.cmdl__qte_livr);
                       
                        $('#prixLigne').val(dataSet.devl_puht);
                        
                    
                        $('#modalLigne').modal('show')
                        
              
                    },
                    error: function (err) {
                        console.log('error: ' , err);
                    }

                })
            })

            //click pour le changement de client 
            $('#ClientClick').on('click',  function(){

                dataFiche = this.value;
                $('#selectContact option').remove();
                $.ajax({
                    type: 'post',
                    url: "AjaxDevisFacture",
                    data:
                    {
                        "AjaxDevis": dataFiche
                    },
                    success: function (data) {
                        dataSet = JSON.parse(data);
                        $('#postCmd').val(dataSet[0].devis__id);
                        $('#postSociety').val(dataSet[1].client__id);
                      
                        $('#titreClient').text( " Commande N°: "+dataSet[0].devis__id);
                        $('#clientName').text(dataSet[1].client__societe)
                        $('#textClient').html('<b>'+ dataSet[1].client__adr1 + ' ' + dataSet[1].client__adr2  +
                        '<br> '+ dataSet[1].client__cp+ ' '+ dataSet[1].client__ville + '</b>');
                        let clientList = dataSet[2];
                        $('#selectContact').append(new Option( "Aucun Contact" ,""));
                        for (let index = 0; index < clientList.length; index++) 
                        {
                            $('#selectContact').append(new Option(clientList[index].contact__nom + " " + 
                            clientList[index].contact__prenom + ' ' +  clientList[index].contact__prenom  + " " +
                             clientList[index].kw__lib , clientList[index].contact__id ));   
                        }
                        $('.selectpicker').selectpicker('refresh'); 
                        if (dataSet[0].devis__contact__id > 1 ) 
                        {
                            $('#selectContact').selectpicker('val',dataSet[0].devis__contact__id);
                        } else  $('#selectContact').selectpicker('val', "");
                        
                        $('#modalContact').modal('show')
                       
              
                    },
                    error: function (err) {
                        console.log('error: ' , err);
                    }

                })
                
            })


             //click pour le changement de TVA.
             $('#buttonModalTVA').on('click' , function()
             {
                 dataFiche = this.value;
                 
                 $.ajax({
                     type: 'post',
                     url: "AjaxDevisFacture",
                     data:
                     {
                         "AjaxDevis": dataFiche
                     },
                     success: function (data) {
                         dataSet = JSON.parse(data);
                         $('#titreTVA').text(" Commande N°: "+dataSet[0].devis__id);
                         $('#hiddenTVA').val(dataSet[0].devis__id);
                         $('#selectTVA').val(dataSet[0].cmd__tva);
                         $('#codeCmdTVA').val(dataSet[0].cmd__code_cmd_client);
                         ckComClient.setData(dataSet[0].devis__note_client);
                         $('#modalTVA').modal('show')
                     },
                     error: function (err) {
                         console.log('error: ' , err);
                     }
                 })
             })

            //  click pour le rajout de lignes:
            $('#addNewItem').on('click', function()
            {
                dataItem = this.value;
                
                $.ajax({
                    type: 'post',
                    url: "AjaxDevisFacture",
                    data:
                    {
                        "AjaxDevis": dataItem
                    },
                    success: function (data) {
                        
                        dataSet = JSON.parse(data);
                        $('#titreItem').text('Ajout article Commande N°: ' + dataSet[0].devis__id);
                        
                        $('#idDevisAddLigne').val(dataSet[0].devis__id);
                        $('#modalItem').modal('show');
                       
                    },
                    error: function (err) {
                        console.log('error: ' , err);
                    }
                })
               
            })
           
            $("html, body").animate({ scrollTop: 0 }, "slow");

        },
        error: function (err) {
            console.log('error: ' + err);
        }
    })
});




 // Attribue automatiquement la classe selected à la première ligne a la table fiche de travail : 
 let selectFirst = function(){
    let firstOne = $('#factureTable').find('tr').eq(1);
    firstOne.addClass('selected');
    let dataRow = factureT.row().data();
  
    $('#iframeFacture').attr('src', '');
    $('#iframeFacture').hide();
    $('#loaderFacture').show();
    // requete Ajax sur le devis selectionné dans la page mes devis : 
    $.ajax({
        type: 'post',
        url: "factureVisio",
        data : 
        {
            "AjaxFT" : dataRow[0]
        },
        success: function(data){
            
            dataSet = JSON.parse(data);
            $('#loaderFacture').hide();
            $('#iframeFacture').html(dataSet);
            $('#iframeFacture').show();


            //click pour chaque ligne
            $('.clickFact').on('click',  function(){

                dataFicheLigne = this.value;
               
                $.ajax({
                    type: 'post',
                    url: "AjaxLigneFT",
                    data:
                    {
                        "AjaxLigneFT": dataFicheLigne
                    },
                    success: function (data) {
                       
                        dataSet = JSON.parse(data);
                        $('#titreLigne').text(dataSet.famille__lib+ " " + dataSet.modele + " "  + dataSet.marque);
                        $('#idCMDL').val(dataSet.devl__id);
                        $('#qteCMD').val(dataSet.devl_quantite);
                        $('#qteLVR').val(dataSet.cmdl__qte_livr);
                        $('#ComTech').html(dataSet.devl__note_interne);
                        if (dataSet.cmdl__note_facture != null) 
                        {
                            ckComFacture.setData(dataSet.cmdl__note_facture);
                        }
                        
                        if (dataSet.cmdl__qte_fact) 
                        {
                            $('#qteFTC').val(dataSet.cmdl__qte_fact);
                        } else  $('#qteFTC').val(dataSet.cmdl__qte_livr);

                       
                        $('#prixLigne').val(dataSet.devl_puht);
                        //function clicks pour les différentes quantité: 

                        $('#modalLigne').modal('show')
                        
              
                    },
                    error: function (err) {
                        console.log('error: ' , err);
                    }

                })
            })

            //click pour le changement de client 
            $('#ClientClick').on('click',  function(){

                dataFiche = this.value;
                $('#selectContact option').remove();
                $.ajax({
                    type: 'post',
                    url: "AjaxDevisFacture",
                    data:
                    {
                        "AjaxDevis": dataFiche
                    },
                    success: function (data) {
                        dataSet = JSON.parse(data);
                        $('#postCmd').val(dataSet[0].devis__id);
                        $('#postSociety').val(dataSet[1].client__id);
                        $('#titreClient').text( " Commande N°: "+dataSet[0].devis__id);
                        $('#clientName').text(dataSet[1].client__societe)
                        $('#textClient').html('<b>'+ dataSet[1].client__adr1 + ' ' + dataSet[1].client__adr2  +
                        '<br> '+ dataSet[1].client__cp+ ' '+ dataSet[1].client__ville + '</b>');
                        let clientList = dataSet[2];
                        $('#selectContact').append(new Option( "Aucun Contact" ,""));
                        for (let index = 0; index < clientList.length; index++) 
                        {
                            $('#selectContact').append(new Option(clientList[index].contact__nom + " " + 
                            clientList[index].contact__prenom + ' ' +  clientList[index].contact__prenom  + " " +
                             clientList[index].kw__lib , clientList[index].contact__id ));   
                        }
                        $('.selectpicker').selectpicker('refresh'); 
                        if (dataSet[0].devis__contact__id > 1 ) 
                        {
                            $('#selectContact').selectpicker('val',dataSet[0].devis__contact__id);
                        } else  $('#selectContact').selectpicker('val', "");
                        
                        $('#modalContact').modal('show')
                       
              
                    },
                    error: function (err) {
                        console.log('error: ' , err);
                    }

                })
            })

            //click pour le changement de TVA.
            $('#buttonModalTVA').on('click' , function()
            {
                dataFiche = this.value;
                
                $.ajax({
                    type: 'post',
                    url: "AjaxDevisFacture",
                    data:
                    {
                        "AjaxDevis": dataFiche
                    },
                    success: function (data) {
                        dataSet = JSON.parse(data);
                        
                        $('#titreTVA').text(" Commande N°: "+dataSet[0].devis__id);
                        $('#hiddenTVA').val(dataSet[0].devis__id);
                        $('#selectTVA').val(dataSet[0].cmd__tva);
                        $('#codeCmdTVA').val(dataSet[0].cmd__code_cmd_client);
                        ckComClient.setData(dataSet[0].devis__note_client);
                    
                        $('#modalTVA').modal('show')
                    },
                    error: function (err) {
                        console.log('error: ' , err);
                    }
                })
            })


             //  click pour le rajout de lignes:
             $('#addNewItem').on('click', function()
             {
                 dataItem = this.value;
                 
                 $.ajax({
                     type: 'post',
                     url: "AjaxDevisFacture",
                     data:
                     {
                         "AjaxDevis": dataItem
                     },
                     success: function (data) {
                         
                         dataSet = JSON.parse(data);
                         $('#titreItem').text('Ajout article Commande N°: ' + dataSet[0].devis__id);
                         $('#idDevisAddLigne').val(dataSet[0].devis__id);
                         $('#modalItem').modal('show');
                        
                     },
                     error: function (err) {
                         console.log('error: ' , err);
                     }
                 })
                
             })



        },
        error: function (err) {
            console.log('error: ' , err);
        }
    })
}

//si la longueur de la table est supérieure a zero je lance la fonction:
if ($('#factureTable').length > 0) {
    selectFirst();
}

//quand je selectionne un article dans le rajout de ligne je transmet la valeur a l'input de désignation: 

$('#choixDesignation').on('change', function()
{
    value = $(this).children("option:selected").text();
    $('#referenceS').val(value);
})


//commandés:
let minCMD = function(){
    $('#minusCMD').on('click' , function()
    {
        let qteMinus =  parseInt($('#qteCMD').val());
        qteMinus = qteMinus -1 ;
        $('#qteCMD').val(qteMinus); 
    })
}
minCMD();


$('#plusCMD').on('click' , function(){
    let qtePlus =  parseInt($('#qteCMD').val());
    qtePlus += 1 ;
    $('#qteCMD').val(qtePlus); 
})

//Livrée:
$('#minusLVR').on('click' , function(){
    let qteMinus =  parseInt($('#qteLVR').val());
    qteMinus -= 1 ;
    $('#qteLVR').val(qteMinus); 
})

$('#plusLVR').on('click' , function(){
    let qtePlus =  parseInt($('#qteLVR').val());
    qtePlus += 1 ;
    $('#qteLVR').val(qtePlus); 
})

//Facturée:
$('#minusFTC').on('click' , function(){
    let qteMinus =  parseInt($('#qteFTC').val());
    qteMinus -= 1 ;
    $('#qteFTC').val(qteMinus); 
})


$('#plusFTC').on('click' , function(){
    let qtePlus =  parseInt($('#qteFTC').val());
    qtePlus += 1 ;
    $('#qteFTC').val(qtePlus); 
})


//ouverture des modaux de societe et de contact (creration)

$('#buttonModalSociete').on('click', function()
{
    $('#modalContact').modal('hide');
    $('#modalSociete').modal('show');
    idPost = $('#postCmd').val();
    $('#nouveauClientId').val(idPost);
})

$('#buttonModalContact').on('click' , function(){
    $('#modalContact').modal('hide');
    
    idSociety = $('#postSociety').val();
    $('#contactCreaPost').val(idSociety);
    titreCrea = $('#clientName').text();
    $('#titleCreaContact').text('Creation de Contact pour : ' + titreCrea );
    idCmd =  $('#postCmd').val();
    $('#idCmdContactCrea').val(idCmd);
    $('#modalContactCrea').modal('show');
})


                        



})