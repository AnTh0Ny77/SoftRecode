$(document).ready(function() {

    $(function () {
        $('[data-toggle="tooltip"]').tooltip()
    })
    
      let tableTransport = $("#transportTable").DataTable({language:{decimal:"",emptyTable:"aucuns résultats",info:"Voir _START_ to _END_ of _TOTAL_ résultats",infoEmpty:"Voir 0 to 0 of 0 résultats",infoFiltered:"(filtré dans _MAX_ total résultats)",infoPostFix:"",thousands:",",lengthMenu:"Voir _MENU_ résultats par pages",loadingRecords:"Loading...",processing:"Processing...",search:"Recherche:",zeroRecords:"Aucun résultats",paginate:{first:"Première",last:"Dernière",next:"Suivante",previous:"Précédente"}},columnDefs:[{targets:[1],visible:!1},{targets:[0 , 4],visible:!1}],order:[[1,"desc"]],paging:!0,info:!1,pageLength:25,retrieve:!0,deferRender:!0,searching:!1});
    
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
        let dataRow = tableTransport.row(this).data();
        let ObjetCmd  = 
        {
            Id: dataRow[0],
            Client: dataRow[3],
            Date: dataRow[2], 
            Data: dataRow = JSON.parse(dataRow[4])
        }
       document.getElementById('JumboCmd').innerHTML =  'Commande N°: ' + ObjetCmd.Id;
       $('#JumboClient').html(ObjetCmd.Client);
       $('#dateTransport').html(ObjetCmd.Date);
       
       let content = '';

       for (key in ObjetCmd.Data) 
       {
           content += ' • ' + ObjetCmd.Data[key].devl_quantite + 'x ' + ObjetCmd.Data[key].devl__designation ;
       }
       $('#JumboDetails').text(content);
    });





     // attribut classe selected: au chargement de la page 
    selectFirst =  function () {
        
        let firstOne = $('#transportTable').find('tr').eq(1);
        firstOne.addClass('selected');
        let dataRow = tableTransport.row().data();
        let ObjetCmd  = 
        {
            Id: dataRow[0],
            Client: dataRow[3],
            Date: dataRow[2], 
            Data: dataRow = JSON.parse(dataRow[4])
        }
       document.getElementById('JumboCmd').innerHTML =  'Commande N°: ' + ObjetCmd.Id;
       $('#JumboClient').html(ObjetCmd.Client);
       $('#dateTransport').html(ObjetCmd.Date);
       
       let content = '';

       for (key in ObjetCmd.Data) 
       {
           content += ' • ' + ObjetCmd.Data[key].devl_quantite + 'x ' + ObjetCmd.Data[key].devl__designation ;
       }
       $('#JumboDetails').text(content);
    };


    selectFirst();



    //num pad javascript: 
    let entry = '';
    $('.calcuClick').on('click', function(){
         entry += this.value;
         entry = entry.toString();
        $('#calc_resultat').val(entry);
    })

    $('#delete').on('click' , function(){
       entry =  entry.substring(0, entry.length - 1);
        $('#calc_resultat').val(entry);
    })

})