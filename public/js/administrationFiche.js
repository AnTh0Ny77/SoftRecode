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

    $('#modalSelection').on('hidden.bs.modal', function (e) {
        console.log('hey')
        $('#list_suggest').html('');
    })



})