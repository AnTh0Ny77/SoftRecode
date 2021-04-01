$(document).ready(function() 
{
        CKEDITOR.config.height = '5em';
        $(function()
        {
              $('.editor').each(function()
              {
                        CKEDITOR.replace( this.id ,
                        {
                                language: 'fr',
                                removePlugins: 'image,justify,pastefromgdocs,pastefromword,about,table,tableselection,tabletools,Source,uicolor',
                                removeButtons : 'PasteText,Paste,Cut,Copy,Blockquote,Source,Subscript,Superscript,Undo,Redo,Maximize,Outdent,Indent,Format,SpecialChar,HorizontalRule,Styles,Strike'
                        });
              })  
        })

        //supprime le border rouge quand on sélectionne la radio box : 
        $('.radioCmd').on('click' , function()
        {
                $(this).parent('div').removeClass('border-danger');     
        })

        //post le formulaire:  
        $('.post_form').on('click' , function()
        {
                //boolean qui servira à validation du form :
                let autorization = true; 
                //verifier toutes les radios-boxs:
                let count_radio = 0; 
                let count_article = 0;
                $('.') 
                $('.radioCmd').each(function()
                { 
                        if ($(this).is(':checked')) count_radio ++;
                })
                console.log(count_article , count_radio);
                if (count_article == count_radio) console.log('tout es check ! ');
                        
                
                
                //variable qui determine ou on redirige form  AVOIR/FACTURE/VALIDATION CLASSIQUE: 

                //POST le formulaire: 
        })

})