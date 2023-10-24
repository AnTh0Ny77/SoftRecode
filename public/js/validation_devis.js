$(document).ready(function() 
{


	function isFileValid() {
		const inputElement = document.getElementById('file_devis');
		
		if (inputElement.files.length === 0) {
			alert("Aucun fichier n'a été sélectionné.");
			return false;
		}
		
		const allowedExtensions = ['pdf', 'png', 'jpg', 'jpeg'];
		const fileName = inputElement.files[0].name;
		const fileExtension = fileName.split('.').pop().toLowerCase();
		
		if (!allowedExtensions.includes(fileExtension)) {
			alert("Le fichier doit être au format PDF, PNG, JPG ou JPEG.");
			return false;
		}
		
		return true;
	}
	
	// CKEDITOR.config.height = '5em';
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

	//initialization des tooltips 
	$(function () {
		$('[data-toggle="tooltip"]').tooltip({ html: true })
	})


	 //array de tous les elements jboxModal client:
	 arrayBox  = $('.jBoxModal_client');
	 //parcours  le tableau jboxModal pour le commentaire interne et client : 
	 for (let index = 0; index < arrayBox.length; index++) 
	 {
		 let attach = '#' + arrayBox[index].value;
   
	   $.ajax(
	   {
	   type: 'post',
	   url: "AjaxLigneFT",
	   data : 
		   {"AjaxLigneFT" : arrayBox[index].value
		   },    
	   success: function(data)
	   {	  
			dataSet = JSON.parse(data); 
			   new jBox('Tooltip', 
			   {
			   width: 300,
			   height: 300,
			   attach: attach + '_client',
			   title: 'Commentaire client ',
			   content: dataSet.devl__note_client,
			   addClass: 'wrapper_tooltips',
				pointer: false
			   });
			   
	   },
	   error: function (err) 
	   {
		   console.log('error: ' + err);
	   }
	   })  
	 }

	 //array de tous les elements jboxModal interne:
	 arrayBox  = $('.jBoxModal_interne');
	 //parcours  le tableau jboxModal pour le commentaire interne et client : 
	 for (let index = 0; index < arrayBox.length; index++) 
	 {
		 let attach = '#' + arrayBox[index].value;
   
	   $.ajax(
	   {
	   type: 'post',
	   url: "AjaxLigneFT",
	   data : 
		   {"AjaxLigneFT" : arrayBox[index].value
		   },    
	   success: function(data)
	   {  
		   dataSet = JSON.parse(data);  

			   new jBox('Tooltip', 
			   {
			   width: 300,
			   height: 300,
			   attach: attach + '_interne',
			   title: 'Commentaire interne ',
			   content: dataSet.devl__note_interne,
			   addClass: 'wrapper_tooltips',
			   pointer: false
			   });	    
	   },
	   error: function (err) 
	   {
		   console.log('error: ' + err);
	   }
	   })  
	 }

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
		$('#nature_demande').val(value);
		$('#tableau_garantie').val('');

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
		// for (var i  in CKEDITOR.instances) 
		// 	{
		// 		let data = CKEDITOR.instances[i].getData();
		// 		let objet_commentaire = 
		// 			{
		// 				id : CKEDITOR.instances[i].name,
		// 				valeur : data
		// 			}
	 
		// 		tableau_des_commentaires.push(objet_commentaire)
		// 	}
		
		//stringifie le contenu et le transmet au input respectifs 
		// $('#tableau_commentaires').val(JSON.stringify(tableau_des_commentaires));
		$('#tableau_garantie').val(JSON.stringify(tableau_des_garanties));
		//poste le formulaire
		if (isFileValid()) {$('#form_validation').submit();}
		
	})


})