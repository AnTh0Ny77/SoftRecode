
    //se sert de l'option selectionné du selectModele pour récuperer les pn correspondants:
    //le select des pn doit etre un select picker pour cette fonction
    //la div qui englobe le pn est le pn wrapper par default elle doit porter la classe d-none 
    let selectModele_2_selectPn = function(selectModele, selectPn,wrapperPn){
        let modele = selectModele.children("option:selected").val();
        $.ajax({
			type: 'post',url: "AjaxPn",data:{"AjaxPn": modele}, success: function (data){					
			    dataSet = JSON.parse(data);
				if ( dataSet.length > 0){
                    wrapperPn.removeClass('d-none');
                    selectPn.find('option').remove();
					selectPn.selectpicker('refresh');
					selectPn.append(new Option('Non spécifié', '0'))
                    for (let index = 0; index < dataSet.length; index++){
						if (dataSet[index].apn__desc_short == null){
							dataSet[index].apn__desc_short = '';
						}
						
						selectPn.append(new Option(dataSet[index].apn__pn_long + " " + dataSet[index].apn__desc_short, dataSet[index].id__pn))
					}
					selectPn.selectpicker('refresh')
					selectPn.selectpicker('val', '0');
				}else{
                    selectPn.find('option').remove();
					selectPn.append(new Option('Non spécifié', '0'))
					selectPn.selectpicker('refresh')
					selectPn.selectpicker('val', '0');
					wrapperPn.addClass('d-none');
                }
            },error: function (err){
				console.log('error: ', err);
			    }
		})
    }

    //met a jour la designation d'un champ texte avec le changement d'un select : 
    let maj_designation = function(text_input, select_input){
        var selected_pn_text = select_input.children("option:selected").text();
		if (selected_pn_text.length > 2 ) {
			text_input.val(selected_pn_text);
		}
    }