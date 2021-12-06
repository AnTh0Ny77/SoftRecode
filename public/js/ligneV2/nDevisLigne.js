


$(document).ready(function() 
{
    //initialization des tooltips 
    $(function () {
	$('[data-toggle="tooltip"]').tooltip({ html: true })
    })
    

    CKEDITOR.config.height = '5em';
    //init du commentaire Client global : 
    if ($('#comClient').length) 
    {	
		CKEDITOR.replace( 'comClient' ,
		{
			language: 'fr',
			removePlugins: 'image,justify,pastefromgdocs,pastefromword,about,table,tableselection,tabletools,Source,uicolor' ,
			removeButtons : 'PasteText,Paste,Cut,Copy,Blockquote,Source,Subscript,Superscript,Undo,Redo,Maximize,Outdent,Indent,Format,SpecialChar,HorizontalRule'
		});				    
    }

	// MAUVAISE OPTIMISIATION DU CODE PLUSIEURS FUNCTIONS SONT UTILISEES / 
	// EST APPLIQUE DURANT LA PREMIERE SELECTION EN CAS DE CREATION DE LIGNE DE DEVIS / 
	let get_pn_and_refresh = function()
	{
		let modele = $('#fmm').children("option:selected").val();
		$.ajax(
			{
				type: 'post',
				url: "AjaxPn",
				data:
				{
					"AjaxPn": modele
				},
				success: function (data) {					
					
					dataSet = JSON.parse(data);
					
					
					if ( dataSet.length > 0 )
					{
						
						$('#wrapper-pn').removeClass('d-none');
						$('#pn-select').find('option').remove();
						$('#pn-select').selectpicker('refresh');
						$("#pn-select").append(new Option('Non spécifié', '0'))
						for (let index = 0; index < dataSet.length; index++)
						{
							if (dataSet[index].apn__desc_short == null)
							{
								dataSet[index].apn__desc_short = '';
							}
							$("#pn-select").append(new Option(dataSet[index].apn__pn_long + " " + dataSet[index].apn__desc_short, dataSet[index].id__pn))
						}
						
						$('#pn-select').selectpicker('refresh')
						$('#pn-select').selectpicker('val', '0');
					}
					else
					{
						$('#pn-select').find('option').remove();
						$("#pn-select").append(new Option('Non spécifié', '0'))
						$('#pn-select').selectpicker('refresh')
						$('#pn-select').selectpicker('val', '0');
						$('#wrapper-pn').addClass('d-none');
					}

				},
				error: function (err) {
					console.log('error: ', err);
				}
			})

	}


	// EST UTILISEE EN CAS DE MODIFICATION DE LIGNE / 
	let get_pn_line_and_refresh = function()
	{
		let id_ligne = $('#boolModif').val();
			$.ajax(
			{
				type: 'post',
				url: "Ajax-pn-ligne",
				data:
				{
					"ligneID": id_ligne
				},
				success: function (data) 
				{
					dataSet = JSON.parse(data);
					console.log( 'hey');
					if (dataSet[1].length > 0) 
					{
						$('#wrapper-pn').removeClass('d-none');
						$('#pn-select').find('option').remove();
						$('#pn-select').selectpicker('refresh');
						$("#pn-select").append(new Option('Non spécifié', '0'))
						CKEDITOR.instances['comClient'].data('');
						for (let index = 0; index < dataSet[1].length; index++)
						{
							
							$("#pn-select").append(new Option(dataSet[1][index].apn__pn_long + " " +  dataSet[1][index].apn__desc_short, dataSet[1][index].id__pn))

						}
						$('#pn-select').selectpicker('refresh');	
						if (dataSet[0].cmdl__pn != null )
						{
								$('#pn-select').selectpicker('val', dataSet[0].cmdl__pn);
						}
						else 
						{
								$('#pn-select').selectpicker('val', '0');
						}
								
					}
				},
				error: function (err) {
					console.log('error: ', err);
				}
			})
	}

	//check quand un changement à lieu sur le select du pn si une photo est disponible :  
	let update_designation_commerciale_pn = function(){
		$('#pn-select').on('change' , function(){
			CKEDITOR.instances['comClient'].setData('');
			let Pn = $(this).children("option:selected").val();
			if (Pn && Pn != '0'){
				$.ajax({
					type: 'post' , url : "Ajax-pn-id" , data : { "pn_id": Pn },
					success: function(data){
						dataSet = JSON.parse(data);
						if (dataSet.apn__design_com.length > 0 ) {
							$('#designation').val(dataSet.apn__design_com);
						}
						if (dataSet.apn__desc_long.length > 0) {
							CKEDITOR.instances['comClient'].insertHtml(dataSet.apn__desc_long);
						
						}
					}
				})
			} 
		})
	}

	update_designation_commerciale_pn();


	let check_pn_photo = function()
	{
		$("#pn-select").on('change' , function()
		{
			
			var id_pn = $(this).children("option:selected").val();
			if (id_pn && id_pn != '0') 
			{
				$.ajax(
					{
						type: 'post',
						url: "Ajax-pn-id",
						data:
						{
							"pn_id": id_pn
						},
						success: function (data) 
						{
							console.log(data);
							dataSet = JSON.parse(data);
							
							if(dataSet.apn__image != null ) 
							{
								$('#div_pn').removeClass('d-none');			
							}
							else 
							{
								$('#div_pn').addClass('d-none');	
							}
						},
						error: function (err) {
							console.log('error: ', err);
						}
					})
			}
		})
	}

	// check_pn_photo();

	if ($('#boolModif').length > 0 )
	{
		get_pn_line_and_refresh();
	}

    if ($('#comInterne').length) 
    {
		CKEDITOR.replace( 'comInterne' ,
		{
			language: 'fr',
			removePlugins: 'image,justify,pastefromgdocs,pastefromword,about,table,tableselection,tabletools,Source,uicolor' ,
			removeButtons : 'PasteText,Paste,Cut,Copy,Blockquote,Source,Subscript,Superscript,Undo,Redo,Maximize,Outdent,Indent,Format,SpecialChar,HorizontalRule'
		});
    }

  let jbox_image =  new jBox('Tooltip',
	{
	    width: 310,
	    height: 310,
	    id: 'jbox_image',
	    attach: '#hover_image',
	    content: ""
	}
    );
    
//selection de l'article dans le select ( Ajax recupère la désignation commerciale si existante)
$('#fmm').on('change', function()
{
	CKEDITOR.instances['comClient'].setData('');
    var selectedArticle = $(this).children("option:selected").text();
	get_pn_and_refresh();
    var id_fmm = $(this).children("option:selected").val();
	$.ajax(
	    {
		type: 'post',
		url: "ajax_idfmm",
		data:
		{
		    "idfmm": id_fmm
		},
		success: function (data) 
		{
		    
		    dataSet = JSON.parse(data); 
		    
		    if (dataSet.afmm__design_com != null) 
		    {
			$("#designation").val(dataSet.afmm__design_com);
		    }
		    else 
		    {
			$("#designation").val(selectedArticle);
		    }
		    if (dataSet.afmm__image != null || dataSet.afmm__image.length == 0 ) 
		    {
				$('#hover_image').removeClass('d-none');
				let html = '<img src="data:image/png;base64,' + dataSet.afmm__image + '" width="270" />';
				jbox_image.setContent(html);    
		    }
		    else
		    {
				$('#hover_image').addClass('d-none');

		    }
		    
		},
		error: function (err) 
		{
		    $("#designation").val(selectedArticle);
		    console.log('error: ' , err);
		}
	    })  
    
})
  



//mise en place des button tooltips pour le com interne: 

  //array de tous les elements jboxModal:
  arrayBox  = $('.jBoxModal');

  //parcours  le tableau jboxModal pour le commentaire interne: 
  for (let index = 0; index < arrayBox.length; index++) 
  {
      let idLigne = arrayBox[index];
      let attach = '#' + idLigne.id;

    $.ajax(
    {
	type: 'post',
	url: "AjaxLigneFT",
	data : 
	    {"AjaxLigneFT" : idLigne.value
	    },    
	success: function(data)
	{
	   
	    dataSet = JSON.parse(data);
	    
		
		if (dataSet.devl__id == parseInt(idLigne.value)) 
		{
		   
		    new jBox('Tooltip', 
		    {
			width: 400,
			height: 300,
			attach: attach,
			title: 'Commentaire Interne',
			content: dataSet.devl__note_interne,
			addClass: 'wrapper_tooltips' , 
				pointer: false
		    });
		    
		} 
	   
	},
	error: function (err) 
	{
	    console.log('error: ' + err);
	}
    })  
  }

//mise en place des tootltips pour le commentaire client:  
//array de tous les elements jboxModal:
arrayBoxClient  = $('.jBoxModalClient');

//parcours  le tableau jboxModal pour le commentaire interne: 
for (let y = 0; y < arrayBoxClient.length; y++) 
{
    let idLigne = arrayBoxClient[y];
    let attach = '#' + idLigne.id;

  $.ajax(
  {
      type: 'post',
      url: "AjaxLigneFT",
      data : 
	  {"AjaxLigneFT" : idLigne.value
	  },    
      success: function(data)
      {
	  dataSet = JSON.parse(data);
	
	      
	      if (dataSet.devl__id == parseInt(idLigne.value)) 
	      {
		 
		  new jBox('Tooltip', 
		  {
		      width: 400,
		      height: 300,
		      attach: attach,
		      title: 'Commentaire Client',
		      content: dataSet.devl__note_client,
			  addClass: 'wrapper_tooltips',
			  pointer: false
		  });
		  
	      } 
	 
      },
      
      error: function (err) 
      {
	  console.log('error: ' + err);
      }
  })  
}
    



    //post le formulaire d'ajout de port automatique : 
    $('.click-port').on('click' , function()
    {
	let value = parseInt(this.value)
	$('#value_port').val(value);
	$('#form_port').submit();
    })

    //active/desactive la ligne concernée:
    $('.click-span').on('click' , function()
    {
	let value = parseInt(this.value);
	$('#value_activate').val(value);
	$('#activate_line').submit();
    })
    
    
})
    
    
    