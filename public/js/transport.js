$(document).ready(function() {

    $(function () {
        $('[data-toggle="tooltip"]').tooltip()
    })
    
      let tableTransport = $("#transportTable").DataTable({language:{decimal:"",emptyTable:"aucuns résultats",info:"Voir _START_ to _END_ of _TOTAL_ résultats",infoEmpty:"Voir 0 to 0 of 0 résultats",infoFiltered:"(filtré dans _MAX_ total résultats)",infoPostFix:"",thousands:",",lengthMenu:"Voir _MENU_ résultats par pages",loadingRecords:"Loading...",processing:"Processing...",search:"Recherche:",zeroRecords:"Aucun résultats",paginate:{first:"Première",last:"Dernière",next:"Suivante",previous:"Précédente"}},columnDefs:[{targets:[1],visible:!1},{targets:[0],visible:!1}],order:[[1,"desc"]],paging:!0,info:!1,pageLength:25,retrieve:!0,deferRender:!0,searching:!1});
    
      idUtilisateur = $('#idUtilisateur').val();


     // attribut classe selected: a la table saisie transport sur le click:
     tableTransport.on('click', 'tr', function () {
        
        $('#iframeTravail').hide();
        if ($(this).hasClass('selected')) {
            $(this).removeClass('selected');
        }
        else if (tableTransport.rows().count() >= 1) {
            tableTransport.$('tr.selected').removeClass('selected');
            $(this).addClass('selected');
        }
        let dataRow =tableTransport.row(this).data();
        $('#iframeTravail').attr('src', '');
        $('#loaderTravail').show();
        
        // requete Ajax sur le devis selectionné dans la page saisie : 
        $.ajax({
            type: 'post',
            url: "AjaxTransport",
            data:
            {
                "Ajaxtransport": dataRow[0]
            },
            success: function (data) {
                dataSet = JSON.parse(data);
                
                $('#loaderTravail').hide();
                $('#iframeTravail').html(dataSet);
                $('#iframeTravail').show();        
            },
            error: function (err) {
                console.log('error: ' + err);
            }
        })
    });

})