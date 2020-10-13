$(document).ready(function() {

    $('#openModal').on('click' , function()
    {
            $.ajax({
                    type: 'post',
                    url: "AjaxSociete",
                    data : {"AjaxSociete" : 7},    
                        success: function(data)
                            {
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

                                    tableClient.on('click' , 'tr' , function()
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
                                       let dataRow = tableClient.row(this).data();
                                       
                                       $('#sociteContact').val(dataRow.client__id);
                                       $('#ModalSociete').modal('hide');
                                    })

                                    $('#ModalSociete').modal('show');
                            },
                        error: function (err) 
                            {
                                console.log('error: ' , err);
                            }
                })
    })

})