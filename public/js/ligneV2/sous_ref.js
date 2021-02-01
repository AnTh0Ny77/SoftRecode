$(document).ready(function() 
{
    //ouvre le modal et transmet l'id ligne à l'input correspondant 
    $('.open_modal').on('click' , function()
    {
        $('#input_id_ref').val(this.value);
        let id_ligne = this.value;
        //recupère les info  de la ligne principale pour la sous-rèf : 
        $.ajax(
            {
                type: 'post',
                url: "AjaxLigneFT",
                data : 
                    {"AjaxLigneFT" : id_ligne
                    },    
                success: function(data)
                { 
                    dataSet = JSON.parse(data);
                    $('#select_sous_ref').selectpicker('val' , dataSet.id__fmm);
                    console.log(dataSet);
                },
                error: function (err) 
                {
                    console.log('error: ' , err);
                }
            })  
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

    //modification des sous-références : 
    $('.click_sous_ref').on('click' , function()
    {
        let id_ligne = this.value;
         //recupère les info  de la ligne principale pour la sous-rèf : 
         $.ajax(
            {
                type: 'post',
                url: "AjaxLigneFT",
                data : 
                    {"AjaxLigneFT" : id_ligne
                    },    
                success: function(data)
                { 
                    //prérempli les champs avec les infos récupérées
                    dataSet = JSON.parse(data);
                    $('#select_modif_sous_ref').selectpicker('val' , dataSet.id__fmm);
                    $('#input_modif_sous_ref').val(dataSet.devl__id);
                    $('#input_modif_sous_ref_cmd').val(dataSet.cmdl__cmd__id);
                    $('#quantite_modif_sous_ref').val(dataSet.devl_quantite);
                    editor_modif_sous_ref.setData(dataSet.devl__note_interne);
                    let sous_garantie = parseInt(dataSet.cmdl__sous_garantie);
                    if (sous_garantie === 1 ) 
                    {
                        $('#modif_sous_ref_garantie').prop('checked', true);
                    }
                    else $('#modif_sous_ref_garantie').prop('checked', false );
                 
                },
                error: function (err) 
                {
                    console.log('error: ' , err);
                }
            })  

        $('#modal_modif_sous_ref').modal('show');
    })

    //fermeture du modal de modif 
    $('#close_modal_modif_sous_ref').on('click' ,function()
    {
        $('#modal_modif_sous_ref').modal('hide');
    })


    //init de commentaire de modificationde sous ref : 
    if ($('#com_modif_sous_ref').length) 
    {
        ClassicEditor       
        .create( document.querySelector( '#com_modif_sous_ref' ) ,{
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
                    editor_modif_sous_ref = newEditor
                } )
             .catch( error =>
             {
                 console.error( error );
             });     
    }

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