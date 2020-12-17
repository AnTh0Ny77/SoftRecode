


$(document).ready(function() {

//init du commentaire Interne global : 
if ($('#globalComInt').length) 
{
    ClassicEditor       
    .create( document.querySelector( '#globalComInt' ) ,{
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
        [
            'heading', '|',  'bold', 'italic', 'bulletedList', 'numberedList' , 'link', '|', 'undo' , 'redo' , 'fontColor'
        ]
         })
         .catch( error =>
         {
             console.error( error );
         });     
}

//init du commentaire Client global : 
if ($('#globalComClient').length) 
{
    ClassicEditor       
    .create( document.querySelector( '#globalComClient' ) ,{
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
        [
            'heading', '|',  'bold', 'italic', 'bulletedList', 'numberedList' , 'link', '|', 'undo' , 'redo' , 'fontColor'
        ]
         })
         .catch( error =>
         {
             console.error( error );
         });     
}



//fonction de remplissage des select contact en fonction de leur client : 
//facturation : 
$('#clientSelect').on('change', function()
{
    var selectedOption = parseInt($(this).children("option:selected").val());
   
    $.ajax({
        type: 'post',
        url: "tableContact",
        data : 
            {
                "AjaxContactTable" : selectedOption
            },
            success: function(data)
            {
               dataSet = JSON.parse(data);
               $('#contactSelect option').remove();
               $('#contactSelect').append(new Option('Aucun', 'Aucun' , false, true));

                    for (let index = 0; index < dataSet.length; index++)
                    {
                        
                        $('#contactSelect').append(new Option(dataSet[index].contact__nom + " " + dataSet[index].contact__prenom + " - "  + dataSet[index].kw__lib ,dataSet[index].contact__id));  
                    }
                    $('.selectpicker').selectpicker('refresh'); 
                    $('#contactSelect').selectpicker('val', 'Aucun');
            },
            error: function (err) 
            {
                console.log('error: ' , err);
            }
        })
})

//livraison : 
$('#clientLivraison').on('change', function()
{
    var selectedOption = parseInt($(this).children("option:selected").val());
    $.ajax(
    {
        type: 'post',
        url: "tableContact",
        data : 
            {
                "AjaxContactTable" : selectedOption
            },
            success: function(data)
            {
               dataSet = JSON.parse(data);
                    $('#contactLivraison option').remove();
                    $('#contactLivraison').append(new Option('Aucun', 'Aucun' , false, true));

                    for (let index = 0; index < dataSet.length; index++)
                    {   
                        $('#contactLivraison').append(new Option(dataSet[index].contact__nom + " " + dataSet[index].contact__prenom + " - "  + dataSet[index].kw__lib ,dataSet[index].contact__id));  
                    }
                    $('.selectpicker').selectpicker('refresh'); 
                    $('#contactLivraison').selectpicker('val', 'Aucun');
            },
            error: function (err) 
            {
                console.log('error: ' , err);
            }
        })
})



    $('#ajax_client_button').on('click' , function()
    {

    })

})


