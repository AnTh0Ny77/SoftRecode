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
    //transmet le text du modèle selectionné a l'input de designation en recuperant la désignation renseignée  : 
    $('#select_sous_ref').on('change' , function()
    {
        var selectedArticle = $(this).children("option:selected").text();
        $("#designation_sous_ref").val(selectedArticle);
        var id_fmm = $(this).children("option:selected").val();
        $.ajax(
            {
                type: 'post',
                url: "ajax_idfmm",
                data:
                {
                    "idfmm": id_fmm
                },
                success: function (data) {

                    dataSet = JSON.parse(data);
                    if (dataSet.afmm__design_com != null) {
                        $("#designation_sous_ref").val(dataSet.afmm__design_com);
                    }
                    else {
                        $("#designation_sous_ref").val(selectedArticle);
                    }

                },
                error: function (err) {
                    $("#designation_sous_ref").val(selectedArticle);
                    console.log('error: ', err);
                }
            })  
    })

    //transmet le text du modèle ou la designation du select au text pour la partie modification : 
    $('#select_modif_sous_ref').on('change' , function()
    {
        var selectedArticle = $(this).children("option:selected").text();
        $("#designation_modif_sous_ref").val(selectedArticle);
        var id_fmm = $(this).children("option:selected").val();
        $.ajax(
            {
                type: 'post',
                url: "ajax_idfmm",
                data:
                {
                    "idfmm": id_fmm
                },
                success: function (data) {

                    dataSet = JSON.parse(data);
                    if (dataSet.afmm__design_com != null) {
                        $("#designation_modif_sous_ref").val(dataSet.afmm__design_com);
                    }
                    else {
                        $("#designation_modif_sous_ref").val(selectedArticle);
                    }

                },
                error: function (err) {
                    $("#designation_modif_sous_ref").val(selectedArticle);
                    console.log('error: ', err);
                }
            })  
    })
    //fonction qui vide le contenu du formulaire : 
    let delete_form = function()
    {
        $('#input_id_ref').val('');
        editor_sous_ref.setData('');
        $('#designation_sous_ref').val('');
    }
    //efface le formulaire de modification :
    let delete_form_modif = function()
    { 
        $('#input_modif_sous_ref').val('');
        $('#input_modif_sous_ref_cmd').val('');
        $('#quantite_modif_sous_ref').val(1);
        $('#designation_modif_sous_ref').val('');
        editor_modif_sous_ref.setData('');
        $('#modif_sous_ref_garantie').prop('checked', false);
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
                    $('#designation_modif_sous_ref').val(dataSet.devl__designation);
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
        delete_form_modif();
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