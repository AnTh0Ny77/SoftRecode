$(document).ready(function() 
{
   //sur chaque frappe j'effectue une recherche des concordances clients:
     $('#client_input').on('keyup' , function()
     {
        let recherche = $('#client_input').val();
        $('#search_list li').remove();
        // a partir de 1 caractère: 
        if (recherche.length > 1 ) 
        { 
            //requete ajax:
            $.ajax(
            {
                type: 'post',
                url: "Ajax_search_client",
                data : 
                {
                    "search" : recherche
                },
                success: function(data)
                {
                    dataSet = JSON.parse(data); 
                    //cree une suggestion pour chaque concordances trouvée: 
                    for (let index = 0; index < dataSet.length; index++)
                    {
                        $('#search_list').append('<li class="list-group-item list-group-item-action click_option" value="'+dataSet[index].client__id+'"> '+ dataSet[index].client__id 
                        + " " + dataSet[index].client__societe+ " " 
                        + dataSet[index].client__cp 
                        + " " + dataSet[index].client__ville 
                        + '</li>')   
                    }
                    //fonction sur le click pour chaque nouvelle option:
                    $('.click_option').on('click' , function()
                    {
                       
                        $('#search_list li').remove();
                        $('#client_input').val('');
                        $('#card_client').toggleClass('d-none');
                        $('#container_forms').toggleClass('d-none');
                        $('#search_client').toggleClass('d-none');
                        $('#hidden_client').val(this.value);
                        //requete ajax secondaire recupère les infos clients principal : 
                        $.ajax(
                            {
                                type: 'post',
                                url: "choixLivraison",
                                data :{ "AjaxLivraison" : this.value },
                                success: function(data)
                                {
                                    dataSet = JSON.parse(data);
                                    $('#titre_client').text(dataSet.client__id + " " + dataSet.client__societe);
                                    $('#societe_client').text(dataSet.client__adr1 + " " + dataSet.client__cp + " - " + dataSet.client__ville);
                                },
                                error: function(err)
                                {
                                    console.log('erreur dans la requete secondaire URL : choixLivraison ' , err);
                                }
                            }) 
                        //requete ajax secondaire recupère les contacts:
                        $.ajax(
                        {
                            type: 'post',
                            url: "tableContact",
                            data :{ "AjaxContactTable" : this.value },
                            success: function(data)
                            {
                                dataSet = JSON.parse(data); 
                                $('#contact_select').append(new Option('Aucun', '' , false, true));
                                for (let index_contact = 0; index_contact <  dataSet.length; index_contact++) 
                                {
                                    $('#contact_select').append(new Option(dataSet[index_contact].contact__nom + " " + dataSet[index_contact].contact__prenom + " " + dataSet[index_contact].kw__lib, dataSet[index_contact].contact__id , false, true));
                                }
                                $('#contact_select option[value=""]').prop('selected', true);
                               
                            },
                            error: function(err)
                            {
                                console.log('erreur dans la requete secondaire URL : tableContact ' , err);
                            }
                        }) 
                        
                    }) 
                },         
                error: function (err) 
                {
                    console.log('erreur dans la requete primaire URL:  Ajax_search_client' , err);
                }
                })
        }
        else 
        {
            $('#search_list li').remove();
        }
      })


     //fonction pour retablir son choix client et reinitialiser le formulaire: 
     $('#client_button').on('click', function()
     {
      
        $('#contact_select option').remove();
        $('#search_list li').remove();
        $('#client_input').val('');
        $('#card_client').toggleClass('d-none');
        $('#search_client').toggleClass('d-none');
        $('#container_forms').toggleClass('d-none');
        tableau_article = [];
        write_array(); 
        
     })

     
     //check si le tableau article est present et crée le formulaire a poster  :  
     let check_post = function()
     {
         if (tableau_article.length > 0 ) 
         {
            $('#print_button').removeClass('d-none');
            $('#print_button').removeAttr("disabled");
         }
         else 
         {
            $('#print_button').addClass('d-none');
            $('#print_button').prop("disabled", true);
         }
     }


     //transmet sur le change de l'article : 
     $('#choixDesignation').on('change' , function()
     {
          let article = $(this).children("option:selected").html();
         $('#designationArticle').val(article);
     })

     let tableau_article = [];
     




     //fonction chargée de d'ecrire le tableau d'article ( recursive => appel delete article qui appelle write-array )
    let write_array = function()
    {
        //vide le formulaire: 
        $("#form_article").html('');
        for (let index = 0; index < tableau_article.length; index++) 
        {
            //on creer une ligne pour chaque article : 
           if(tableau_article[index].typ == 'ECH')
           {
               text_variable = 'Echange'
           }
           else
           {
               text_variable = 'Retour'
           } 
            let new_line = '<div class="py-4 mb-2 card green-card-recode bg-secondary py-2 d-flex flex-row justify-content-around"><h5 class="text-primary font-weight-bold" >'+ text_variable  +
                ' <br> <span class="text-secondary">quantité: ' + tableau_article[index].qte
                + '</span></h5> <div class="mx-3"><b class="mx-3">' + tableau_article[index].design
                +'</b></div><div> <button type="button" class=" btn-recode-danger rounded-pill shadow-rec click_article" value="'
            +index+'"><i class="far fa-times"></i></button></div>  </div>';
          
           $("#form_article").append(new_line);
        
          
        }

        //fonction qui implemente la fonction de supression d'article et rapelle write post à l'interieur : 
        delete_article();
        //check le tableau et rend accèssible ou non le post : 
        check_post();

        //converti le tableau en objet JSON 
        let json_array = JSON.stringify(tableau_article);
        //si le tableau n'est pas vide je transmet à l'input : 
        if (tableau_article.length > 0 ) 
        {
            $('#json_array').val(json_array);
        }
        else 
        {
            $('#json_array').val('');
        }
        
    }




    //implente la fonction de suppression au button des articles : 
     let delete_article = function()
     {
        $('.click_article').on('click', function()
        {
           
           tableau_article.splice(this.value, 1);
           write_array(); 
        })
        
     }

     //post d'un article :
     $('#post_article').on('click', function()
     {
       

         let id__fmm =  $('#choixDesignation').children("option:selected").val(); 
         let designation = $('#designationArticle').val();
         let quantite = $('#quantiteLigne').val();
         let type = $('#typeLigne').val();
         let pn_ligne = $('#pn-select').children("option:selected").val();

         console.log(pn_ligne);
        
         if ( designation.length > 1 && quantite > 0) 
         {
             let article = 
             {
                 id : id__fmm ,
                 design : designation ,
                 qte : quantite ,
                 typ : type ,
                 pn: pn_ligne
               
             }
             tableau_article.push(article);
             write_array();
             $('#designationArticle').val('');   
             $('#quantiteLigne').val(1); 
             $('#modal_ligne').modal('hide');
         }
         else 
         {
             alert('formulaire incorrect')
         }
         
     })
     
    //innit de l'éditeur de texte de la zone de commentaire: 
    CKEDITOR.config.height = '5em';
    CKEDITOR.replace('commentaire_interne',{
        language: 'fr',
        removePlugins: 'justify,pastefromgdocs,pastefromword,about,table,tableselection,tabletools,Source,uicolor',
        removeButtons: 'PasteText,Paste,Cut,Copy,Blockquote,Source,Subscript,Superscript,Undo,Redo,Maximize,Outdent,Indent,Format,SpecialChar,HorizontalRule'
    });

    // public/js/functions/articles.js:

    //select du modele 
    let select_modele = $('#choixDesignation');
    //select du pn:
    let select_pn =  $('#pn-select');
    //wrapper du pn 
    let wrapper_pn = $('#wrapper-pn');
    //designation text = 
    let designation = $('#designationArticle');

    //met à jour le select du pn sur chaque changement du select du modele: 
    select_modele.on('change' , function(){
        selectModele_2_selectPn(select_modele,select_pn,wrapper_pn);
    })
    //met a jour la designation sur chaque changement de pn :  
    select_pn.on('change', function (){
        maj_designation(designation, select_pn);
    })
    
})
    
    
    