$(document).ready(function() 
{
    ClassicEditor
    .create( document.querySelector( '#comInterne' ) , 
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




$('#clickLivraison').on('click' , function()
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
              $('#MajLivraison').val(dataRow.client__id);
             
              //enleve le disabled du button de choix client 
              $('#buttonPost').removeAttr('disabled');
             
              
            })


           
           
        },
        error: function (err) {
            console.log('error: ' , err);
        }

    })
})








})