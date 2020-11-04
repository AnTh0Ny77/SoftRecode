


$(document).ready(function() 
{
  
    //init du commentaire Client global : 
    if ($('#comClient').length) 
    {
        ClassicEditor       
        .create( document.querySelector( '#comClient' ) ,{
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


    if ($('#comInterne').length) 
    {
        ClassicEditor       
        .create( document.querySelector( '#comInterne' ) ,{
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
    
    
    $('#fmm').on('change', function()
{
    var selectedArticle = $(this).children("option:selected").text();
    $("#designation").val(selectedArticle);
})
  
    
    
    
    })
    
    
    