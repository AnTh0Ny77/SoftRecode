$(document).ready(function(){
    const prod = "http://192.168.1.105/api";
    const local = "http://localhost/RESTapi/";
    let body = {
        "secret" : "heAzqxwcrTTTuyzegva^5646478§§uifzi77..!yegezytaa9143ww98314528", 
        'sav__cli_id' :  $('#cli__id').val()
    }

    var Table = null;
    $.ajax(prod + '/boutiqueSossuke', {
            type: 'POST',
            method: "POST",
            crossDomain: true,
            data: JSON.stringify(body),
            success: function (data, status, xhttp) {
                let dataSet = extractObjectValues(data.data);
                let results = [];
                dataSet.forEach(element => {
                   let familleString =  trouverValeur(familles, element[12].sar__famille );
                   let etatString  =  trouverValeur(etat , element[3]);
                   let etatFinal = renderDivEtat(element[3] , etatString );
                   let ref =  familleString + ' <br>  <b>' +   element[12].sar__ref_constructeur + ' ' +  element[12].sar__marque + '</b>';
                   let gar = 'Aucune '
                   if (element[7] != null) {
                    gar = element[7] + ' mois ' +  element[8] + ' € HT'
                   }
                  
                   let temp = [ref , etatFinal ,  '<b>' + element[4] + '€</b> HT' ,  element[5] , gar , element[12].sar__image]; 
                   results.push(temp);
                });
                console.log(dataSet);
                Table = $('#table_bot').DataTable({
                    paging: true,
                    order: [[0, 'desc']] ,
                    lengthMenu: [ 5],
                    data: results,
                    columns: [
                        { title: 'Reference' },
                        { title: 'Etat' },
                        { title: 'Prix' },
                        { title: 'Memo' },
                        { title: 'Garantie ' },
                        { title: '' , 'render' : function(data){
                            if(data != "" && data != null){
                                return '<img src="public/img/boutique/'+ data +'"  width="40px">';
                            }else {
                                return '<img src="public/img/boutique/no.png"  width="40px">';
                            }
                            
                        }   },
                          
                    ],
                    createdRow: function (row, data, index) {
                        $(row).attr('data-toggle', "modal");
                        $(row).attr('data-target', "#modalModif");
                        $(row).css('cursor', 'pointer');
                        $(row).on('click', function () {
                        })
                    },
                    language: {
                        lengthMenu: "Voir _MENU_ articles par page",
                        zeroRecords: "Aucuns résultats",
                        info: "Page: _PAGE_ sur _PAGES_ au total",
                        infoEmpty: "Aucuns articles",
                        infoFiltered: "(résultats sur _MAX_ articles disponibles)",
                        search: "Rechercher"
                    }
                });
            },
            error: function (jqXhr, textStatus, errorMessage) {
                results = jqXhr.responseJSON.msg;

            }

        });


        let renderImage = function(name){
            $.ajax({
                url : 'public/img/boutique/' + name,
                async: false ,
                success: function (data) {
                    var img = $('<img >');
                    img.attr('src', data);
                    return img;
                }
            });
        }

        let renderDivEtat = function(etat , string  ){

            switch (etat ) {
                case 'NEU':
                    return '<div class="past_pink"> '+string+'</div>'
                    break;
                case 'MRK':
                    return '<div class="past_greeen"> '+string+'</div>'
                    break;
                case 'CON':
                    return '<div class="past_pastel"> '+string+'</div>'
                    break;
                case 'OCC':
                    return '<div class="past_purple"> '+string+'</div>'
                    break;
                default:
                    return '<div class="past_pink"> '+string+'</div>'
                    break;
            }

        }
        
        let extractObjectValues = function (arr) {
            const result = arr.map(obj => Object.values(obj));
            return result;
        }

        function trouverValeur(objet, cle) {
            if (objet.hasOwnProperty(cle)) {
              return objet[cle];
            } else {
              return 'Non Renseigné';
            }
          }



    const familles =  {
        'ACC' :	'Accessoire / Option',	
        'BLC' : 'Lecteur code barre'  ,
        'BPT' :	'PDA et Tablette' ,
        'BTE' : 'Terminal embarqué' , 	
        'BTM':	 'Terminal mobile' ,
        'CON':'Consommable',
        'ICA' :	'Imprimante Caisse' ,
        'ILZ' :	'Imprimante laser' ,
        'IMA' :	'Imprimante Matriciel' ,
        'ITH' 	:'Imprimante thermique',
        'MCL' :	'Client léger' ,
        'MEC'	: 'Ecran' ,
        'MPC'	:'Ordinateur fixe' ,
        'MPO' :	'Ordinateur portable' ,
        'PID':	'Pièce détachée' ,
        'RES'	: 'Réseau' ,
        'SER' :	'Service' ,
        'SOF' :	'Soft' ,
        'TEL' :	'Téléphonie'	    	
    }   

   const etat = {
        'CON' :	'consommable compatible',	
        'MRK' : 'consommable à la marque' ,	
        'NEU' :  'matériel neuf' ,	
        'OCC' :	'matériel reconditionné'	
   }


})