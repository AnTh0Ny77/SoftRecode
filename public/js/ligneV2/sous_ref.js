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
                    $('#quantite_sous_ref').val( dataSet.devl_quantite);
                    get_pn_sousRef_and_refresh();
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

                    get_pn_sousRef_and_refresh();
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
                    get_pn_sousRef_M_and_refresh();
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
        CKEDITOR.instances.com_sous_ref.setData('');
        $('#designation_sous_ref').val('');
    }
    //efface le formulaire de modification :
    let delete_form_modif = function()
    { 
        $('#input_modif_sous_ref').val('');
        $('#input_modif_sous_ref_cmd').val('');
        $('#quantite_modif_sous_ref').val(1);
        $('#designation_modif_sous_ref').val('');
       
        CKEDITOR.instances.com_modif_sous_ref.setData('');
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
                    $('#quantite_modif_sous_ref').val(parseInt(dataSet.devl_quantite));
                    $('#designation_modif_sous_ref').val(dataSet.devl__designation);
                    // editor_modif_sous_ref.setData(dataSet.devl__note_interne);
                    CKEDITOR.instances.com_modif_sous_ref.setData(dataSet.devl__note_interne);
                    let sous_garantie = parseInt(dataSet.cmdl__sous_garantie);
                    if (sous_garantie === 1 ) 
                    {
                        $('#modif_sous_ref_garantie').prop('checked', true);
                    }
                    else $('#modif_sous_ref_garantie').prop('checked', false );


                    get_pn_line_sr_and_refresh(id_ligne);
                 
                },
                error: function (err) 
                {
                    console.log('error: ' , err);
                }
            })  

        $('#modal_modif_sous_ref').modal('show');
    })

    //recupère les pn dispo pour la sous ref  : 
    let get_pn_sousRef_and_refresh = function () {
        let modele = $('#select_sous_ref').children("option:selected").val();
        $.ajax(
            {
                type: 'post',
                url: "AjaxPn",
                data:{"AjaxPn": modele},
                success: function (data){
                    dataSet = JSON.parse(data);
                    if (dataSet.length > 0){
                        $('#wrapper-pn-sr').removeClass('d-none');
                        $('#pn-select-sr').find('option').remove();
                        $('#pn-select-sr').selectpicker('refresh');
                        $("#pn-select-sr").append(new Option('Non spécifié', '0'))
                        for (let index = 0; index < dataSet.length; index++){
                            if (dataSet[index].apn__desc_short == null){
                                dataSet[index].apn__desc_short = '';
                            }
                            $("#pn-select-sr").append(new Option(dataSet[index].apn__pn_long + " " + dataSet[index].apn__desc_short, dataSet[index].id__pn))
                        }
                        $('#pn-select-sr').selectpicker('refresh')
                        $('#pn-select-sr').selectpicker('val', '0')
                    }
                    else {
                        $('#pn-select-sr').find('option').remove();
                        $("#pn-select-sr").append(new Option('Non spécifié','0'))
                        $('#pn-select-sr').selectpicker('refresh')
                        $('#pn-select-sr').selectpicker('val', '0')
                        $('#wrapper-pn-sr').addClass('d-none')
                    }
                },
                error: function (err) {
                    console.log('error: ', err);
                }
            })

    }

    $('#pn-select-sr').on('change' ,function(){
        let text = $('#pn-select-sr').children("option:selected").text();
        $('#designation_sous_ref').val(text)
    })

    $('#pn-select-sr-m').on('change', function(){
        let text = $('#pn-select-sr-m').children("option:selected").text();
        $('#designation_modif_sous_ref').val(text)
    })

    //recupre les pn dispo et selectionne en cas de modif de sous ref  :  
    let get_pn_sousRef_M_and_refresh = function () {
        let modele = $('#select_modif_sous_ref').children("option:selected").val();
        $.ajax({
                type: 'post',
                url: "AjaxPn",
                data:{"AjaxPn": modele},
                success: function (data){
                    dataSet = JSON.parse(data);
                    if (dataSet.length > 0){
                        $('#wrapper-pn-sr-m').removeClass('d-none');
                        $('#pn-select-sr-m').find('option').remove();
                        $('#pn-select-sr-m').selectpicker('refresh');
                        $("#pn-select-sr-m").append(new Option('Non spécifié', '0'))
                        for (let index = 0; index < dataSet.length; index++) {
                            if (dataSet[index].apn__desc_short == null){
                                dataSet[index].apn__desc_short = '';
                            }
                            $("#pn-select-sr-m").append(new Option(dataSet[index].apn__pn_long + " " + dataSet[index].apn__desc_short, dataSet[index].id__pn))
                        }
                        $('#pn-select-sr-m').selectpicker('refresh')
                        $('#pn-select-sr-m').selectpicker('val', '0');
                    }
                    else {
                        $('#pn-select-sr-m').find('option').remove();
                        $("#pn-select-sr-m").append(new Option('Non spécifié', '0'))
                        $('#pn-select-sr-m').selectpicker('refresh')
                        $('#pn-select-sr-m').selectpicker('val', '0');
                        $('#wrapper-pn-sr-m').addClass('d-none');
                    }
                },
                error: function (err) {
                    console.log('error: ', err);
                }
            })

    }

    //ouverture du modal de modif : pn 
    let get_pn_line_sr_and_refresh = function (idLigne) {
        let id_ligne = idLigne
        $.ajax({
                type: 'post',
                url: "Ajax-pn-ligne",
                data:{"ligneID": id_ligne},
                success: function (data) {
                    dataSet = JSON.parse(data);
                    if (dataSet[1].length > 0){
                        $('#wrapper-pn-sr-m').removeClass('d-none');
                        $('#pn-select-sr-m').find('option').remove();
                        $('#pn-select-sr-m').selectpicker('refresh');
                        $("#pn-select-sr-m").append(new Option('Non spécifié', '0'))
                        for (let index = 0; index < dataSet[1].length; index++){
                            $("#pn-select-sr-m").append(new Option(dataSet[1][index].apn__pn_long + " " + dataSet[1][index].apn__desc_short, dataSet[1][index].id__pn))
                        }
                        $('#pn-select-sr-m').selectpicker('refresh');
                        if (dataSet[0].cmdl__pn != null){
                            $('#pn-select-sr-m').selectpicker('val', dataSet[0].cmdl__pn);
                        }
                        else{
                            $('#pn-select-sr-m').selectpicker('val', '0');
                        }
                    }
                },
                error: function (err){
                    console.log('error: ', err);
                }
            })
    }

    //fermeture du modal de modif 
    $('#close_modal_modif_sous_ref').on('click' ,function(){
        delete_form_modif();
        $('#modal_modif_sous_ref').modal('hide');
    })
    //init de commentaire de modificationde sous ref : 
    if ($('#com_modif_sous_ref').length){
        CKEDITOR.replace( 'com_modif_sous_ref',{
			language: 'fr',
			removePlugins: 'image,justify,pastefromgdocs,pastefromword,about,table,tableselection,tabletools,Source,uicolor' ,
			removeButtons : 'PasteText,Paste,Cut,Copy,Blockquote,Source,Subscript,Superscript,Undo,Redo,Maximize,Outdent,Indent,Format,SpecialChar,HorizontalRule'
		});
    }
    //init du commentaire interne sous-ref : 
    if ($('#com_sous_ref').length) {
        CKEDITOR.replace( 'com_sous_ref' ,{
			language: 'fr',
			removePlugins: 'image,justify,pastefromgdocs,pastefromword,about,table,tableselection,tabletools,Source,uicolor' ,
			removeButtons : 'PasteText,Paste,Cut,Copy,Blockquote,Source,Subscript,Superscript,Undo,Redo,Maximize,Outdent,Indent,Format,SpecialChar,HorizontalRule'
		});
    }

})