//recherhe sur le click du button recherche :  
$('#btn_search_client').on('click', function () {
    let string_recherche = $('#client_input').val();
    $("body").css("cursor", "wait");
    //requete ajax qui recupère les résultats de la requete  : 
    $.ajax({
        type: 'post',
        url: "Ajax_search_client_devis",
        data:
        {
            "search": string_recherche
        },
        success: function (data) {
            //deserialize objet json (tableau d'objets)
            dataSet = JSON.parse(data);
            //si la réponse est nulle : 
            if (dataSet.length < 1) {
                show_alert('Aucun résultat');
                delete_liste_faturation();
                $("body").css("cursor", "auto");
            }
            //si la réponse est unique je selectionne de suite le client  : 
            else if (dataSet.length == 1) {
                delete_liste_faturation();
                let client = dataSet[0].client__id ;
                let display_client = '(' + dataSet[0].client__id + ') ' + dataSet[0].client__societe + '  ' + dataSet[0].client__cp + ' - ' + dataSet[0].client__ville + '';
                add_client_selected(client, display_client);
                $("body").css("cursor", "auto");
            }
            // si la reponse est multiple je propose une selection : 
            else {
                //si il y a 20 résultats ou + je préviens l'utilisateur d'utiliser des critères plus précis:
                if (dataSet.length == 12) {
                    $('#alert_long_facture').text('');
                }
                else {
                    $('#alert_long_facture').text(' ');
                }
                //cree la liste client :
                delete_liste_faturation();
                create_list_client(dataSet);
                //attribue la fonction click a chaque button créé afin de selectionner le client dynamiquement : 
                $('.click-facturation').on('click', function () {
                    delete_liste_faturation();
                    let client = parseInt(this.value);
                    //boucle dans le tableau de resultat pour retrouver la concordance: 
                    for (let index = 0; index < dataSet.length; index++) {
                        if (dataSet[index].client__id == client) {
                            let display_client = '(' + dataSet[index].client__id + ') ' + dataSet[index].client__societe + '  ' + dataSet[index].client__cp + ' - ' + dataSet[index].client__ville + '';
                            add_client_selected(client, display_client);
                        }
                    }
                   
                    //ferme le modal : 
                    $('#modal_choix_client').modal('hide');
                })
                //j'ouvre le modal : 
                $('#modal_choix_client').modal('show');
            }
            $("body").css("cursor", "auto");
        },
        error: function (err) {
            console.log('error: ', err);
        }
    })
})
//fonction qui boucle sur la liste de client et crée les suggestions : 
let create_list_client = function (tableau_client) {
    //boucle sur le tableau de résultats : 
    for (let index = 0; index < tableau_client.length; index++) {
        //crée un élément de liste avec chaque client correspondant : 
        $text_list = ' <button class=" btn pastille-recode  click-facturation" value="' + tableau_client[index].client__id + '"><b> (' + tableau_client[index].client__id + ') ' + tableau_client[index].client__societe + '  ' + tableau_client[index].client__cp + '-' + tableau_client[index].client__ville + ' </b></button>';
        $("#list_client_facture").append('<li class="d-flex my-2 justify-content-between align-items-center"> '
            + $text_list + '</li>');
    }
}

//fonction qui ouvre le modal d'alerte en affichant la variable demandée :
let show_alert = function (text) {
    $('#alert_text').text(text);
    $('#modal_alert').modal('show');
} 
//fonction qui efface la liste présente dans le modal pour les suggestions de client facturés: 
let delete_liste_faturation = function () {
    $("#list_client_facture li").remove();
}

//fonction qui affiche inverse l'input et le button de selection de client : 
let add_client_selected = function(client , display_client){
    $('#client_input').addClass('d-none');
    $('#btn_search_client').addClass('d-none');
    $('#Client').val(client);
    $('#nClient').val(client);
    $('#display_client').val(display_client);
    $('#display_client').removeClass('d-none');
    $('#remove_client').removeClass('d-none');
}
let remove_client_selected = function(){
    $('#client_input').removeClass('d-none');
    $('#btn_search_client').removeClass('d-none');
    $('#Client').val('');
    $('#nClient').val('');
    $('#display_client').val('');
    $('#display_client').addClass('d-none');
    $('#remove_client').addClass('d-none');
}

$('#remove_client').on('click' , function(){
    remove_client_selected();
})

//preselectionne le client :
let preset_client  = $('#presetClient').val();
if (preset_client.length > 4 ){
    let string_recherche = preset_client;
    $("body").css("cursor", "wait");
    //requete ajax qui recupère les résultats de la requete  : 
    $.ajax({
        type: 'post',
        url: "Ajax_search_client_devis",
        data:
        {
            "search": string_recherche
        },
        success: function (data) {
            //deserialize objet json (tableau d'objets)
            dataSet = JSON.parse(data);
            //si la réponse est nulle : 
            if (dataSet.length < 1) {
                show_alert('Aucun résultat');
                delete_liste_faturation();
                $("body").css("cursor", "auto");
            }
            //si la réponse est unique je selectionne de suite le client  : 
            else if (dataSet.length == 1) {
                delete_liste_faturation();
                let client = dataSet[0].client__id;
                let display_client = '(' + dataSet[0].client__id + ') ' + dataSet[0].client__societe + '  ' + dataSet[0].client__cp + ' - ' + dataSet[0].client__ville + '';
                add_client_selected(client, display_client);
                $("body").css("cursor", "auto");
            }
            // si la reponse est multiple je propose une selection : 
            else {
                //si il y a 20 résultats ou + je préviens l'utilisateur d'utiliser des critères plus précis:
                if (dataSet.length == 12) {
                    $('#alert_long_facture').text('');
                }
                else {
                    $('#alert_long_facture').text(' ');
                }
                //cree la liste client :
                delete_liste_faturation();
                create_list_client(dataSet);
                //attribue la fonction click a chaque button créé afin de selectionner le client dynamiquement : 
                $('.click-facturation').on('click', function () {
                    delete_liste_faturation();
                    let client = parseInt(this.value);
                    //boucle dans le tableau de resultat pour retrouver la concordance: 
                    for (let index = 0; index < dataSet.length; index++) {
                        if (dataSet[index].client__id == client) {
                            let display_client = '(' + dataSet[index].client__id + ') ' + dataSet[index].client__societe + '  ' + dataSet[index].client__cp + ' - ' + dataSet[index].client__ville + '';
                            add_client_selected(client, display_client);
                        }
                    }

                    //ferme le modal : 
                    $('#modal_choix_client').modal('hide');
                })
                //j'ouvre le modal : 
                $('#modal_choix_client').modal('show');
            }
            $("body").css("cursor", "auto");
        },
        error: function (err) {
            console.log('error: ', err);
        }
    })
}


$(window).keydown(function (event) {
    if ((event.keyCode == 13)) {
        if (event.shiftKey){

        }else {
            event.preventDefault();
        }
       
    }
});