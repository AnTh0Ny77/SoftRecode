$(document).ready(function()
{

   

    // choix du client lors de la creation de fiche 
    $('#targetModal').on('click' , function()
    {
        dataFiche = 7 ;
        $.ajax({
            type: 'post',
            url: "AjaxSociete",
            data:
            {
                "AjaxSociete": dataFiche
            },
            success: function (data) {

                dataSet = JSON.parse(data);
                arrayClient = dataSet;
                tableClient=$("#ClientTable").DataTable(
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

                  
                $('#modalLivraison').modal('show');
                //click selection
                tableClient.on('click' , 'tr' , function()
                {
                   //classe selected
                   if ($(this).hasClass('selected')) 
                   {
                       $(this).removeClass('selected');
                       $('#buttonPost').prop("disabled", true);
                   }
                   else if (tableClient.rows().count() >= 1) 
                   {
                       tableClient.$('tr.selected').removeClass('selected');
                       
                       $(this).addClass('selected');
                       $('#buttonPost').removeAttr('disabled');
                   }
                   let dataRow = tableClient.row(this).data();
                  $('#creaLivraison').val(dataRow.client__id);
                 
                  //enleve le disabled du button de choix client 
                  $('#buttonPost').removeAttr('disabled');
                  //click qui permet le post definitif en enlevant le disabled du 2eme button :
                  $('#buttonPost').on('click' , function()
                  {
                     
                    $('#PreSelected').html( dataRow.client__societe + "<br> " + dataRow.client__ville + " " + dataRow.client__cp)
                    $('.trClick').removeClass('selected');
                    $('#validCrea').removeAttr('disabled');
                    $('#modalLivraison').modal('hide');
                  })
                  
                })


               
               
            },
            error: function (err) {
                console.log('error: ' , err);
            }

        })
    })


  $('#targetAddLines').on('click' , function()
  {
      $('#modalAddLines').modal('show');
  })

 
$('#choixDesignation').on('change' ,function () 
{
    
    var test = $( "#choixDesignation option:selected" ).text();
    console.log(test);
    $('#designationArticle').val(test);

  });



  
  $('#targetContact').on('click' , function()
  {
    dataFiche = this.value;
    
    $.ajax({
        type: 'post',
        url: "AjaxClientContact",
        data:
        {
            "AjaxDevis": dataFiche
        },
        success: function (data) 
        {
           
            // int de la table contact :
            tableContact=$("#ContactTable").DataTable({language:{decimal:"",emptyTable:"aucuns résultats",info:"Voir _START_ to _END_ of _TOTAL_ résultats",infoEmpty:"Voir 0 to 0 of 0 résultats",infoFiltered:"(filtré dans _MAX_ total résultats)",infoPostFix:"",thousands:",",lengthMenu:"Voir _MENU_ résultats par pages",loadingRecords:"Loading...",processing:"Processing...",search:"Recherche:",zeroRecords:"Aucun résultats",paginate:{first:"Première",last:"Dernière",next:"Suivante",previous:"Précédente"}},columns:[{data:"contact__id"},{data:"contact__nom"},{data:"kw__lib"}],paging:!0,info:!0,deferRender:!0,retrieve:!0,deferRender:!0,searching:!1});
            dataSet = JSON.parse(data);
            tableContact.clear().draw();
            dataSetContact = dataSet[1];
            tableContact.rows.add(dataSetContact).draw(); 
            tableContact.on('click' , 'tr' , function()
                {
                   //classe selected
                   if ($(this).hasClass('selected')) 
                   {
                       $(this).removeClass('selected');
                       $('#btnContact').prop("disabled", true);
                   }
                   else if (tableContact.rows().count() >= 1) 
                   {
                       tableContact.$('tr.selected').removeClass('selected');
                       
                       $(this).addClass('selected');
                       $('#btnContact').removeAttr('disabled');
                   }
                   let dataRow = tableContact.row(this).data();
                  
                   $('#ChangeContact').val(dataRow.contact__id);
                   
                   
                })
            $('#modalContact').modal('show');
               
        },
        error: function (err) {
            console.log('error: ' , err);
        }

    })
      
  })
    

})