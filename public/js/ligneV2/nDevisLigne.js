


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
    var selectedArticle = $(this).children("option:selected").text();
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
		    console.log(dataSet);
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
			content: dataSet.devl__note_interne
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
		      content: dataSet.devl__note_client
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
    
    
    