$(document).ready(function() {

//initialization de  tout les tooltips 
$(function () {
    $('[data-toggle="tooltip"]').tooltip()
})

  let factureT = $("#factureTable").DataTable({language:{decimal:"",emptyTable:"aucuns résultats",info:"Voir _START_ to _END_ of _TOTAL_ résultats",infoEmpty:"Voir 0 to 0 of 0 résultats",infoFiltered:"(filtré dans _MAX_ total résultats)",infoPostFix:"",thousands:",",lengthMenu:"Voir _MENU_ résultats par pages",loadingRecords:"Loading...",processing:"Processing...",search:"Recherche:",zeroRecords:"Aucun résultats",paginate:{first:"Première",last:"Dernière",next:"Suivante",previous:"Précédente"}},columnDefs:[{targets:[1],visible:!1},{targets:[0],visible:!1}],order:[[1,"desc"]],paging:!0,info:!1,pageLength:25,retrieve:!0,deferRender:!0,searching:!1});

  idUtilisateur = $('#idUtilisateur').val();


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