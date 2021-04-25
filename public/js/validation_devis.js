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

		if ($('.article').length  == $('input:radio:checked').length)  $('.post_form').removeAttr('Disabled') ;                     
	})

	//rend disponible les boutons si pas d'extension : 
	if ($('.border-danger').length == 0 ) $('.post_form').removeAttr('Disabled');

       
	//fonction du post du formulaire vers le module : 
	$('.post_form').on('click', function()
	{
		//transmet la nature de l 'action : avoir facture etc ... 
		let value = $(this).val();
		$('#nature_demande').val(value)

		//différents tableau à mettre a jour : 
		let tableau_des_garanties = [] ;
		let tableau_des_commentaires = [];

		//boucle sur les radio et crée un tableau avec objet : id / valeur :
		for (let index = 0; index < $('.radioCmd').length; index++) 
		{
		      // si c'est bien chécké : 
		      if ($('.radioCmd')[index].checked == true) 
		      {
				let objet_garantie = 
				{
					id : $('.radioCmd')[index].name,
					valeur : $('.radioCmd')[index].value
				}

				tableau_des_garanties.push(objet_garantie)
		      }      
		}

		// boucle sur les commentaires et crée un tableau id /valeur : 
		for (var i  in CKEDITOR.instances) 
			{
				let data = CKEDITOR.instances[i].getData();
				let objet_commentaire = 
					{
						id : CKEDITOR.instances[i].name,
						valeur : data
					}
	 
				tableau_des_commentaires.push(objet_commentaire)
			}
		
		//stringifie le contenu et le transmet au input respectifs 
		$('#tableau_commentaires').val(JSON.stringify(tableau_des_commentaires));
		$('#tableau_garantie').val(JSON.stringify(tableau_des_garanties));
		//poste le formulaire
		$('#form_validation').submit();
	})


})