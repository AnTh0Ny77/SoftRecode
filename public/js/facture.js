$(document).ready(function() {

//initialization de  tout les tooltips 
$(function () {
    $('[data-toggle="tooltip"]').tooltip()
})

  let factureT = $("#factureTable").DataTable({language:{decimal:"",emptyTable:"aucuns résultats",info:"Voir _START_ to _END_ of _TOTAL_ résultats",infoEmpty:"Voir 0 to 0 of 0 résultats",infoFiltered:"(filtré dans _MAX_ total résultats)",infoPostFix:"",thousands:",",lengthMenu:"Voir _MENU_ résultats par pages",loadingRecords:"Loading...",processing:"Processing...",search:"Recherche:",zeroRecords:"Aucun résultats",paginate:{first:"Première",last:"Dernière",next:"Suivante",previous:"Précédente"}},columnDefs:[{targets:[1],visible:!1},{targets:[0],visible:!1}],order:[[1,"desc"]],paging:!0,info:!1,pageLength:25,retrieve:!0,deferRender:!0,searching:!1});

  idUtilisateur = $('#idUtilisateur').val();



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
    // requete Ajax sur le devis selectionné dans la page mes devis : 
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
                        $('#titreLigne').text(dataSet.famille__lib+ " " + dataSet.modele + " "  + dataSet.marque);
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

})