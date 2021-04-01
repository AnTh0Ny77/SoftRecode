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

})