$(document).ready(function() {

    let ficheT = $("#ficheTable").DataTable({language:{decimal:"",emptyTable:"aucuns résultats",info:"Voir _START_ to _END_ of _TOTAL_ résultats",infoEmpty:"Voir 0 to 0 of 0 résultats",infoFiltered:"(filtré dans _MAX_ total résultats)",infoPostFix:"",thousands:",",lengthMenu:"Voir _MENU_ résultats par pages",loadingRecords:"Loading...",processing:"Processing...",search:"Recherche:",zeroRecords:"Aucun résultats",paginate:{first:"Première",last:"Dernière",next:"Suivante",previous:"Précédente"}},columnDefs:[{targets:[1],visible:!1},{targets:[0],visible:!1}],order:[[1,"desc"]],paging:!0,info:!1,pageLength:25,retrieve:!0,deferRender:!0,searching:!0});


    ficheT.on('click', 'tr', function () 
    {
        
        $('#iframeFiche').hide();
        if ($(this).hasClass('selected')) 
        {
            $(this).removeClass('selected');
        }
        else if (ficheT.rows().count() >= 1) 
        {
            ficheT.$('tr.selected').removeClass('selected');
            $(this).addClass('selected');
        }
        let dataRow =ficheT.row(this).data();
        $('#titreAdmin').text( 'Abonnement commande N°: ' + dataRow[0]);
        $('#hiddenId').val(dataRow[0]);
       
    })



     // Attribue automatiquement la classe selected à la première ligne a la table fiche de travail : 
     let selectFirst = function(){
        let firstOne = $('#ficheTable').find('tr').eq(1);
        firstOne.addClass('selected');
        let dataRow = ficheT.row().data();
        $('#titreAdmin').text( 'Abonnement commande N°: ' + dataRow[0]);
        $('#hiddenId').val(dataRow[0]);
       
       
    }

    //si la longueur de la table est supérieure a zero je lance la fonction:
    if ($('#ficheTable').length > 0) {
        selectFirst();
    }

})    