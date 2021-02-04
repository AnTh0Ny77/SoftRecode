


$(document).ready(function() {

    $(function () {
        $('[data-toggle="tooltip"]').tooltip()
      })

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
    let optionText = $(this).children("option:selected").val();
    if (optionText != 'Aucun') 
    {
        $('#societe_input').val(selectedOption);
        $('#button_crea_contact').removeClass('d-none');
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
    }
    else
    {
        $('#button_crea_contact').addClass('d-none');
    }
   
   
})

//livraison : 
$('#clientLivraison').on('change', function()
{
    let optionText = $(this).children("option:selected").val();
    var selectedOption = parseInt($(this).children("option:selected").val());

    if (optionText != 'Aucun') 
    {
        $('#button_contact_crea_livraison').removeClass('d-none');
        $('#societe_input_livraison').val(selectedOption);
        console.log(selectedOption);
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
    }
    else
    {
        $('#button_contact_crea_livraison').addClass('d-none');
    }
    
})


//fonction qui vide les inputs: 
let deleteInput = function()
{
    $('#prenomContact').val('');
    $('#telContact').val('');
    $('#faxContact').val('');
    $('#mailContact').val(''); 
    
}



//creation de contact : 
$('#postContact').on('click' , function()
{
    let fonction = $('#inputStateContact').val();
    let nom = $('#nomContact').val();
    let prenom = $('#prenomContact').val();
    let civilite = $('#inputCiv').val();
    let tel = $('#telContact').val();
    let fax = $('#faxContact').val();
    let mail = $('#mailContact').val(); 
    let societe = $('#societe_input').val();

   
    
    if (fonction && nom.length > 1 && civilite) 
    {
        $.ajax(
            {
                type: 'post',
                url: "ajax_crea_contact_devis",
                data : 
                    {
                        "fonction" : fonction,
                        "nom" : nom,
                        "prenom" : prenom,
                        "civilite" : civilite , 
                        "tel" : tel ,
                        "fax" : fax , 
                        "mail" : mail ,
                        "societe" : societe
                    },
                    success: function(data)
                    {
                       dataSet = JSON.parse(data);
                       id_contact = parseInt(dataSet);
                       deleteInput();
                       $.ajax(
                        {
                            type: 'post',
                            url: "tableContact",
                            data : 
                                {
                                    "AjaxContactTable" : societe
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
                                        $('#contactSelect').selectpicker('val',id_contact);
                                        $('.modal').modal('hide');
                                },
                                error: function (err) 
                                {
                                    console.log('error: ' , err);
                                }
                            })
                            
                    },
                    error: function (err) 
                    {
                        console.log('error: ' , err);
                    }
                })
    }
    else 
    {
        alert('Le nom , la fonction et la civilité sont obligatoires')
    }
})


let deleteInputLivraison = function()
{
    $('#prenomContact_livraison').val('');
    $('#nomContact_livraison').val('');
    $('#telContact_livraison').val('');
    $('#faxContact_livraison').val('');
    $('#mailContact_livraison').val(''); 
    
}

//creation de contact livraison : 
$('#postContact_livraison').on('click' , function()
{
    let fonction = $('#input_livraison_fonction').val();
    let nom = $('#nomContact_livraison').val();
    let prenom = $('#prenomContact_livraison').val();
    let civilite = $('#inputCiv_livraison').val();
    let tel = $('#telContact_livraison').val();
    let fax = $('#faxContact_livraison').val();
    let mail = $('#mailContact_livraison').val(); 
    let societe = $('#societe_input_livraison').val();

   
    
    if (fonction && nom.length > 1 && civilite) 
    {
        $.ajax(
            {
                type: 'post',
                url: "ajax_crea_contact_devis",
                data : 
                    {
                        "fonction" : fonction,
                        "nom" : nom,
                        "prenom" : prenom,
                        "civilite" : civilite , 
                        "tel" : tel ,
                        "fax" : fax , 
                        "mail" : mail ,
                        "societe" : societe
                    },
                    success: function(data)
                    {
                       console.log(data);
                       dataSet = JSON.parse(data);
                       id_contact = parseInt(dataSet);
                       deleteInputLivraison();
                       $.ajax(
                        {
                            type: 'post',
                            url: "tableContact",
                            data : 
                                {
                                    "AjaxContactTable" : societe
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
                                        $('#contactLivraison').selectpicker('val',id_contact);
                                        $('.modal').modal('hide');
                                },
                                error: function (err) 
                                {
                                    console.log('error: ' , err);
                                }
                            })
                            
                    },
                    error: function (err) 
                    {
                        console.log('error: ' , err);
                    }
                })
    }
    else 
    {
        alert('Le nom , la fonction et la civilité sont obligatoires')
    }
})


  

    //ajoute la liste client complète dans les 2 select picker: 
    $('#ajax_client_button').on('click' , function()
    {
        $.ajax(
        {
            type: 'post',
            url: "AjaxSociete",
            data : 
            {
                "AjaxLivraison" : 7
            }, 

            beforeSend: function() 
            {
            $('#loading').show();
            },

            complete: function()
            {
            $('#loading').hide();
            }, 

            success: function(data)
            {
                dataSet = JSON.parse(data);
                $('#text_nombre').text('Toutes')
                $('#clientSelect option').remove();
                $('#clientSelect').append(new Option('Aucun', 'Aucun' , false, true));
                
                $('#clientLivraison option').remove();
                $('#clientLivraison').append(new Option('Indentique', 'Aucun' , false, true));

                $('#contactSelect option').remove();
                $('#contactSelect').append(new Option('Aucun', 'Aucun' , false, true));

                $('#contactLivraison option').remove();
                $('#contactLivraison').append(new Option('Aucun', 'Aucun' , false, true));

                for (let index = 0; index < dataSet.length; index++)
                {
                  
                    $('#clientSelect').append(new Option(dataSet[index].client__societe + " ("+dataSet[index].client__id+") " + dataSet[index].client__ville + " " + dataSet[index].client__cp  ,dataSet[index].client__id));
                    $('#clientLivraison').append(new Option(dataSet[index].client__societe + " ("+dataSet[index].client__id+") " + dataSet[index].client__ville + " " + dataSet[index].client__cp  ,dataSet[index].client__id));
                }

                $('.selectpicker').selectpicker('refresh'); 
                $('#clientSelect').selectpicker('val', 'Aucun' );
                $('#clientLivraison').selectpicker('val', 'Aucun' );
                $('#contactSelect').selectpicker('val', 'Aucun');
                $('#contactLivraison').selectpicker('val', 'Aucun');
                $('#ajax_client_button').hide();
            },
                    
            error: function (err) 
            {
                console.log('error: ' + err);
            }
        })
    
    })

})


