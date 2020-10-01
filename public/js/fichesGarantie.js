$(document).ready(function()
{

    //instancie l'éditor dans le modal: 
    ClassicEditor                   
            .create( document.querySelector('#ComInt') , 
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
                                [ 'heading', '|',  'bold', 'italic', 'bulletedList', 'numberedList' , 'link', '|', 'undo' , 'redo' , "imageUpload", 'fontColor']
                })
                .then( newEditor => 
                    {
                        ligneDit = newEditor;
                    } )
                .catch( error => 
                {
                    console.error( error );
                });    


    $('.clickTech').on('click', function()
    {
        dataFiche = this.value;
        $.ajax({
            type: 'post',
            url: "AjaxLigneFT",
            data:
            {
                "AjaxLigneFT": dataFiche
            },
            success: function (data) 
            {
                dataSet = JSON.parse(data);
                $('#titreComLigne').text(dataSet.famille__lib+ " " + dataSet.modele + " "  + dataSet.marque);

                    if (dataSet.devl__note_interne) 
                    {
                        ligneDit.setData(dataSet.devl__note_interne);
                    }

                    $('#hiddenLigne').val(dataSet.devl__id);
                    console.log($('#hiddenLigne'));
                    $('#ModalEditor').modal('show');
      
            },
            error: function (err) {
                console.log('error: ' , err);
            }
        })
    })


    $('#targetModal').on('click' , function()
    {
        dataFiche = $('#idRetour').val() ;
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
                   console.log(dataRow.client__id);
                   $('#changeLivraisonRetour').val(dataRow.client__id);
                   console.log( $('#changeLivraison').val());
                   
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

  
  
    

})