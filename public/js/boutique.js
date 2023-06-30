$(document).ready(function(){
    const prod = "http://192.168.1.105/api";
    // const prod = "http://localhost/RESTapi/";
    let body = {
        "secret" : "heAzqxwcrTTTuyzegva^5646478§§uifzi77..!yegezytaa9143ww98314528", 
        'sav__cli_id' :  $('#cli__id').val()
    }

    var Table = null;


    let renderSAVTable = function(){
        
    }


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
                    gar = element[7] + ' mois ' +  element[8] + ' € HT<br>'
                   } 
                    if (element[9] != null) {
                        gar += element[9] + ' mois ' + element[10] + ' € HT'
                   }   
                    let dataN = new Date();
                    dataN = dataN.getFullYear() + "-" + (dataN.getMonth() + 1) + "-" + dataN.getDate()
                    let background = comparerDates(dataN, element[11])
                    
                    let date = '';
                    if (background) {
                         date = '<span style="color:red">' + element[11] + '</span>';    
                    }else{
                        date = '<span>' + element[11] + '</span>'; 
                    }
                   
                    let temp = [JSON.stringify(element) , ref, etatFinal, '<b>' + element[4] + '€</b> HT<br>' + date ,  element[5] , gar , element[12].sar__image]; 
                   results.push(temp);
                });
                Table = $('#table_bot').DataTable({
                    paging: true,
                    order: [[0, 'desc']] ,
                    lengthMenu: [ 5],
                    bLengthChange : false ,
                    data: results,
                    columns: [
                        { "visible": false },
                        { title: 'Reference' },
                        { title: 'Etat' },
                        { title: 'Prix/date' },
                        { title: 'Memo' },
                        { title: 'EXT de Garantie' },
                        { title: '' , 'render' : function(data){
                            if(data != "" && data != null){
                                return '<img src="public/img/boutique/'+ data +'"  width="40px">';
                            }else {
                                return '<img src="public/img/boutique/no.png"  width="40px">';
                            }
                        }  },
                          
                    ],
                    createdRow: function (row, data, index) {
                        $(row).attr('data-toggle', "modal");
                        $(row).attr('data-target', "#modalSavEdit");
                        $(row).css('cursor', 'pointer');
                        
                       
                        $(row).on('click', function () {
                            temp = JSON.parse($(data)[0]);
                           
                            $('#sav__ref_id_r').selectpicker('deselectAll');
                            $('#sav__ref_id_r').selectpicker('val', temp[2]);
                            $('#sav__ref_id_r').selectpicker('refresh');
                            $('#sav__etat_r').selectpicker('deselectAll');
                            $('#sav__etat_r').selectpicker('val', temp[3]);
                            $('#sav__etat_r').selectpicker('refresh');
                            $('#sav__gar_std_r').selectpicker('deselectAll');
                            if (  temp[6]!= null ) {
                                $('#sav__gar_std_r').selectpicker('val', temp[6]);
                            }else {
                                $('#sav__gar_std_r').selectpicker('val', 0);
                            }
                            $('#sav__gar_std_r').selectpicker('refresh');
                            $('#sav__gar1_mois_r').selectpicker('deselectAll');
                            if (temp[7] != null) {
                                $('#sav__gar1_mois_r').selectpicker('val', temp[7]);
                            }else{
                                $('#sav__gar1_mois_r').selectpicker('val', 0);
                            }
                            $('#sav__gar1_mois_r').selectpicker('refresh');
                            $('#sav__gar2_mois_r').selectpicker('deselectAll');
                            if (temp[9] != null) {
                                $('#sav__gar2_mois_r').selectpicker('val', temp[9]);
                            }else {
                                $('#sav__gar2_mois_r').selectpicker('val', 0);
                            }
                            $('#sav__gar2_mois_r').selectpicker('refresh');
                            $('#sav__gar1_prix_r').val(temp[8]);
                            $('#sav__gar2_prix_r').val(temp[10]);
                            $('#sav__id_r').val(temp[0]);
                            $('#sav__memo_recode_r').val(temp[5]);
                            $('#sav__prix_r').val(temp[4]);
                            $('#sav__dlv_r').val(temp[11]);
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

        function comparerDates(date1, date2) {
            const timestamp1 = new Date(date1).getTime();
            const timestamp2 = new Date(date2).getTime();

            return timestamp1 > timestamp2;
        }


        let conditionRequest = function(){
            let body = {
                "secret" : "heAzqxwcrTTTuyzegva^5646478§§uifzi77..!yegezytaa9143ww98314528", 
                'sco__cli_id' :  $('#cli__id').val()
            }
            $.ajax(prod + '/boutiqueSossuke', {
                type: 'POST',
                method: "POST",
                crossDomain: true,
                data: JSON.stringify(body),
                success: function (data, status, xhttp) {
                    
                    let dataSet = data.data;
                    if (dataSet) {
                        let string = 'Non renseigné';
                        if (dataSet.sco__type_port != null) {
                            $('#sco__type_port').val(dataSet.sco__type_port);
                        }

                        $('#sco__cli_id_fact').val(dataSet.sco__cli_id_fact);

                        if (dataSet.sco__type_port == 'FRNCO') {
                            string = 'Franco de port'
                        } else if (dataSet.sco__type_port == 'FRCOA') {
                            string = 'Franco à ' + dataSet.sco__francoa + ' €';
                            if (dataSet.sco__francoa) {
                                $('#sco__francoa').val(dataSet.sco__francoa);
                            }

                        } else if (dataSet.sco__type_port == 'NOFRC') {
                            string = 'Pas de Franco';
                        }
                        let prix = 'Non Renseigné';
                        if (dataSet.sco__prix_port > 0) {
                            prix = dataSet.sco__prix_port + ' €';
                            $('#sco__prix_port').val(dataSet.sco__prix_port);
                        }
                        let vue = 'Non Renseigné';
                        $('#sco__vue_ref').val('NON');
                        if (dataSet.sco__vue_ref) {
                            $('#sco__vue_ref').val(dataSet.sco__vue_ref);
                            vue = dataSet.sco__vue_ref;
                        }
                        $('#sco__type_port_d').html('<i class="fas fa-fighter-jet"></i> Type de port : <b>' + string + '</b>');
                        $('#sco__prix_port_d').html('<i class="fas fa-euro-sign"></i> prix port :  <b>' + prix + '</b>');
                        $('#sco__vue_ref_d').html('<i class="fas fa-eye"></i> Vue sur les ref : <b>' + vue + '</b>');
                        $('#alert_condition').text('');
                    }
                    
                },
                error: function (jqXhr, textStatus, errorMessage) {
                    results = jqXhr.responseJSON.msg;
                    
                }
            });
        }

        conditionRequest();

        $('#button_condition_modal').on('click' , function(){
            conditionRequest();
        })

        $('#boutton_condition').on('click' , function(){
            let verif = verifyCondition()
            if (verif) {
                $('#alert_condition').text(verif);
            }else {
                let body = {
                    "sco__cli_id_r": $('#cli__id').val() , 
                    "sco__type_port" : $('#sco__type_port').val(),
                    "sco__cli_id_fact" : $('#sco__cli_id_fact').val() ,
                    "sco__francoa" : $('#sco__francoa').val(),
                    "sco__prix_port" : $('#sco__prix_port').val(),
                    "sco__vue_ref" : $('#sco__vue_ref').val() , 
                    "secret" : "heAzqxwcrTTTuyzegva^5646478§§uifzi77..!yegezytaa9143ww98314528"
                };
                $.ajax(prod + '/boutiqueSossuke', {
                    type: 'POST',
                    method: "POST",
                    crossDomain: true,
                    async: false ,
                    data: JSON.stringify(body),
                    success: function (data){
                        console.log(data)
                        conditionRequest();
                        $('.modal').modal('hide');
                        $('#alert_condition').text('');
                    },
                    error: function (jqXhr) {
                        results = jqXhr.responseJSON.msg;
                        console.log(results);
                    }
                });
            }
           
        })

        

        let verifyCondition = function(){
            if ( !valideNom($('#sco__cli_id_fact').val()) || isNaN($('#sco__cli_id_fact').val())) {
                return 'L ID de la societe facturée semble etre erroné'
            }
            if ($('#sco__prix_port').val() && isNaN($('#sco__prix_port').val())) {
                return 'Le prix du port n est pas un nombre '
            }
            if ($('#sco__type_port').val() == 'FRCOA' ) {
                if ( !$('#sco__francoa').val() || isNaN($('#sco__francoa').val())) {
                    return 'Le prix à partir duquel le franco s applique n est pas correctement renseigné'
                }
                
            }
            return false;
        }

        let valideNom = function($nom){
            if($nom.length > 6 &&  $nom.length <= 0){
                return (false)
            }
            return (true)
        }

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

            switch (etat) {
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

        
        $('#boutton_sav').on('click' , function(){

                let verif  = verifySavForm();
                if (verif) {
                    $('#alert_sav').text(verif);
                }else {
                    verif  = verifyGarantie();
                    if (verif) {
                        $('#alert_sav').text(verif);
                    }else{
                        let body =  renderBodySav()
                        $.ajax(prod + '/boutiqueSossuke', {
                            type: 'POST',
                            method: "POST",
                            crossDomain: true,
                            async: false ,
                            data: JSON.stringify(body),
                            success: function (data){
                                $('.modal').modal('hide');
                                $('#alert_sav').text('');
                                window.location.href = 'displaySocieteMyRecode?cli__id=' + $('#cli__id').val();
                            },
                            error: function (jqXhr) {
                                results = jqXhr.responseJSON.msg;
                                console.log(results);
                            }
                        });
                        
                    }
                }
        })

        $('#boutton_sav_r').on('click' , function(){

            let verif  = verifySavForm_r();
            if (verif) {
                $('#alert_sav_r').text(verif);
            }else {
                verif  = verifyGarantie_r();
                if (verif) {
                    $('#alert_sav_r').text(verif);
                }else{
                    let body =  renderBodySav_r()
                    $.ajax(prod + '/boutiqueSossuke', {
                        type: 'POST',
                        method: "POST",
                        crossDomain: true,
                        async: false ,
                        data: JSON.stringify(body),
                        success: function (data){
                            $('.modal').modal('hide');
                            $('#alert_sav').text('');
                            window.location.href = 'displaySocieteMyRecode?cli__id=' + $('#cli__id').val();
                        },
                        error: function (jqXhr) {
                            results = jqXhr.responseJSON.msg;
                            console.log(results);
                        }
                    });
                    
                }
            }
    })


        let renderBodySav = function(){
            let gar1 = null ;
            let prix1 = null;
            let gar2 = null; 
            let prix2 = null ;

            if ($('#sav__gar2_mois').val() > 0 &&  $('#sav__gar1_mois').val() == 0) {
                gar1 = $('#sav__gar2_mois').val() 
                prix1 = $('#sav__gar2_prix').val()
            }else if ($('#sav__gar2_mois').val() > 0 &&  $('#sav__gar1_mois').val() > 0){
                gar1 = $('#sav__gar1_mois').val() 
                prix1 = $('#sav__gar1_prix').val()
                gar2 = $('#sav__gar2_mois').val() 
                prix2 = $('#sav__gar2_prix').val()
            }else if ($('#sav__gar2_mois').val() == 0 &&  $('#sav__gar1_mois').val() > 0){
                gar1 = $('#sav__gar1_mois').val() 
                prix1 = $('#sav__gar1_prix').val()
            }
            $body = {
                "secret" : "heAzqxwcrTTTuyzegva^5646478§§uifzi77..!yegezytaa9143ww98314528" , 
                "sav__cli_id" : $('#cli__id').val() , 
                "sav__ref_id" : $('#sav__ref_id').val(),
                "sav__etat" : $('#sav__etat').val(),
                "sav__prix" : $('#sav__prix').val(),
                "sav__memo_recode" : $('#sav__memo_recode').val(),
                "sav__gar_std" : $('#sav__gar_std').val(),
                "sav__dlv" : $('#sav__dlv').val() , 
                'sav__gar1_mois' : gar1 , 
                'sav__gar1_prix' : prix1 ,
                'sav__gar2_mois' : gar2 , 
                'sav__gar2_prix' : prix2 , 
                'sav__post' : true 
            }
            return $body;
        }


        let renderBodySav_r = function(){
            let gar1 = null ;
            let prix1 = null;
            let gar2 = null; 
            let prix2 = null ;

            if ($('#sav__gar2_mois_r').val() > 0 &&  $('#sav__gar1_mois_r').val() == 0) {
                gar1 = $('#sav__gar2_mois_r').val() 
                prix1 = $('#sav__gar2_prix_r').val()
            }else if ($('#sav__gar2_mois_r').val() > 0 &&  $('#sav__gar1_mois_r').val() > 0){
                gar1 = $('#sav__gar1_mois_r').val() 
                prix1 = $('#sav__gar1_prix_r').val()
                gar2 = $('#sav__gar2_mois_r').val() 
                prix2 = $('#sav__gar2_prix_r').val()
            }else if ($('#sav__gar2_mois_r').val() == 0 &&  $('#sav__gar1_mois_r').val() > 0){
                gar1 = $('#sav__gar1_mois_r').val() 
                prix1 = $('#sav__gar1_prix_r').val()
            }
            $body = {
                "secret" : "heAzqxwcrTTTuyzegva^5646478§§uifzi77..!yegezytaa9143ww98314528" , 
                'sav__id' : $('#sav__id_r').val() ,
                "sav__put" : 'ok',
                "sav__cli_id" : $('#cli__id').val() , 
                "sav__ref_id" : $('#sav__ref_id_r').val(),
                "sav__etat" : $('#sav__etat_r').val(),
                "sav__prix" : $('#sav__prix_r').val(),
                "sav__memo_recode" : $('#sav__memo_recode_r').val(),
                "sav__gar_std" : $('#sav__gar_std_r').val(),
                "sav__dlv" : $('#sav__dlv_r').val() , 
                'sav__gar1_mois' : gar1 , 
                'sav__gar1_prix' : prix1 ,
                'sav__gar2_mois' : gar2 , 
                'sav__gar2_prix' : prix2 
            }
            return $body;
        }

        let verifySavForm = function(){
            if (!$('#sav__prix').val()) {
                return 'Le prix n est pas indiqué'
            }
            if ($('#sav__prix').val() && isNaN($('#sav__prix').val())) {
                return 'Le prix nest pas indiqué ou est incorrect'
            }
            if (!$('#sav__dlv').val()) {
                return 'La date limite de vente est obligatoire'
            }
            return false;
        }

        let verifySavForm_r = function(){
            if (!$('#sav__prix_r').val()) {
                return 'Le prix n est pas indiqué'
            }
            if ($('#sav__prix_r').val() && isNaN($('#sav__prix').val())) {
                return 'Le prix nest pas indiqué ou est incorrect'
            }
            if (!$('#sav__dlv_r').val()) {
                return 'La date limite de vente est obligatoire'
            }
            return false;
        }

        let verifyGarantie_r = function(){
            let value1 = $('#sav__gar1_mois_r').val();
            if(value1> 0 ){
                if (!$('#sav__gar1_prix_r').val()) {
                    return 'Le prix de la garantie n est pas indiqué'
                }
                if ($('#sav__gar1_prix_r').val() && isNaN($('#sav__gar1_prix_r').val())) {
                    return 'Le prix de la garantie n est pas correct'
                }
            }
            let value2 = $('#sav__gar2_mois_r').val();
            if(value2 > 0 ){
                if (!$('#sav__gar2_prix_r').val()) {
                    return 'Le prix de la garantie n est pas indiqué'
                }
                if ($('#sav__gar2_prix_r').val() && isNaN($('#sav__gar2_prix_r').val())) {
                    return 'Le prix de la garantie n est pas correct'
                }
            }
            return false ;
    }

        let verifyGarantie = function(){
                let value1 = $('#sav__gar1_mois').val();
                if(value1> 0 ){
                    if (!$('#sav__gar1_prix').val()) {
                        return 'Le prix de la garantie n est pas indiqué'
                    }
                    if ($('#sav__gar1_prix').val() && isNaN($('#sav__gar1_prix').val())) {
                        return 'Le prix de la garantie n est pas correct'
                    }
                }
                let value2 = $('#sav__gar2_mois').val();
                if(value2 > 0 ){
                    if (!$('#sav__gar2_prix').val()) {
                        return 'Le prix de la garantie n est pas indiqué'
                    }
                    if ($('#sav__gar2_prix').val() && isNaN($('#sav__gar2_prix').val())) {
                        return 'Le prix de la garantie n est pas correct'
                    }
                }
                return false ;
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