$(document).ready(function() 
{

    //init du commentaire Client global : 
if ($('#commentaire_client').length) 
{
    ClassicEditor       
    .create( document.querySelector( '#commentaire_client' ) ,{
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

let chekifitracom = function ()
{
   let intracom = $('#intracom_input').val();
   let selected_option = $('#select_tva').children("option:selected").val();

    if (parseInt(selected_option) == 1 || intracom.length > 4 ) 
   {
        $('#div_intracom').removeClass('d-none');
        $('#intracom_input').prop('required', true);
   }
   else 
   {
        $('#div_intracom').addClass('d-none');
        $('#intracom_input').prop('required', false);
    }
}

chekifitracom();


    $('#select_tva').on('change', function()
    {
        var selectedOption = parseInt($(this).children("option:selected").val());
        if (parseInt(selectedOption) ==  1 ) 
        {
            $('#div_intracom').removeClass('d-none');
            $('#intracom_input').prop('required', true);
          
        } 
        else
        {
            $('#div_intracom').addClass('d-none');
            $('#intracom_input').prop('required', false);
        }
    })


  
})