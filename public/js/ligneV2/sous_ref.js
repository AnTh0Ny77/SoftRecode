$(document).ready(function() 
{

    //ouvre le modal et transmet l'id ligne à l'input correspondant 
    $('.open_modal').on('click' , function()
    {
        $('#input_id_ref').val(this.value);
        $('#modal_sous_ref').modal('show');
    })
    //transmet le text du modèle selectionné a l'input de designation 
    $('#select_sous_ref').on('change' , function()
    {
        var selectedArticle = $(this).children("option:selected").text();
        $("#designation_sous_ref").val(selectedArticle);
    })
    //fonction qui vide le contenu du formulaire : 
    let delete_form = function()
    {
        $('#input_id_ref').val('');
        editor_sous_ref.setData('');
        $('#designation_sous_ref').val('');
    }
    //vide les inputs en cas de fermeture du modal :
    $('#close_modal_sous_ref').on('click', function()
    {
        delete_form();
        $('#modal_sous_ref').modal('hide');
    })

    //init du commentaire interne sous-ref : 
    if ($('#com_sous_ref').length) 
    {
        ClassicEditor       
        .create( document.querySelector( '#com_sous_ref' ) ,{
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
             .then( newEditor => 
                {
                    editor_sous_ref = newEditor;
                } )
             .catch( error =>
             {
                 console.error( error );
             });     
    }

})