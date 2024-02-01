$(document).ready(function() 
{
//fontion qui selectionne le client et crée les suggestion dans le select : 
let select_client_facturation = function(client)
{
        //si le resultat est le meme que le client deja selectionné je crée une animation pour signaler a l'utilisateur que le résultat est identique: 
        if (client.client__id == $('#clientSelect').val()) 
        {
                $('#rotate_facturation').toggleClass('rotated');

        }
        //sinon je selectionne le nouveau résultat :
        else 
        {
                //cosmetique rouge/vert : 
                $('#span_facturation').removeClass('text-danger');
                $('#span_facturation').addClass('text-success');
                //attribue l'id client a l'input pour le post :
                $('#societe_input').val(client.client__id);
                $('#clientSelect').val(client.client__id);
                //verifie que le post est possible : 
                verifie_post();
                //indication visuelle pour l'utilisateur : 
                $('#span_facturation').html(
                        '<b> (' + client.client__id + ') ' + client.client__societe + '  ' + client.client__cp + '-' + client.client__ville + ' </b> <i class="fas fa-check"></i>');
                //gestion des contacts : 
                $('#contactSelect option').remove();
                //option pas de contacts : 
                $('#contactSelect').append(new Option('Aucun', 'Aucun', false, true));
                //boucle sur la liste des contacts présents et crée les options adéquetes : 
                for (let index = 0; index < client.contact_list.length; index++) {
                        $('#contactSelect').append(
                                new Option(client.contact_list[index].contact__nom
                                        + " " + client.contact_list[index].contact__prenom + " - "
                                        + client.contact_list[index].kw__lib, client.contact_list[index].contact__id));
                }
                //rafraichi le select picker :
                $('.selectpicker').selectpicker('refresh');
                //valeur par default : aucun :
                $('#contactSelect').selectpicker('val', 'Aucun');
        }
        
}
//fontion qui selectionne le client et crée les suggestion dans le select : 
let select_client_livraison = function (client) {
        //cosmetique rouge/vert : 
        $('#span_livraison').removeClass('text-danger');
        $('#span_livraison').addClass('text-success');
        //attribue l'id client a l'input pour le post :
        $('#societe_input_livraison').val(client.client__id);
        $('#clientLivraison').val(client.client__id);
        //vérifie que la creation de contact est possible : 
        verifie_livraison();
        //indication visuelle pour l'utilisateur : 
        $('#span_livraison').html(
        '<b> (' + client.client__id + ') ' + client.client__societe + '  ' + client.client__cp + '-' + client.client__ville + ' </b> <i class="fas fa-check"></i>');
        //gestion des contacts : 
        $('#contactLivraison option').remove();
        //option pas de contacts : 
        $('#contactLivraison').append(new Option('Aucun', 'Aucun', false, true));
        //boucle sur la liste des contacts présents et crée les options adéquetes : 
        for (let index = 0; index < client.contact_list.length; index++) 
        {
                $('#contactLivraison').append(
                        new Option(client.contact_list[index].contact__nom
                        + " " + client.contact_list[index].contact__prenom + " - "
                        + client.contact_list[index].kw__lib, client.contact_list[index].contact__id));
        }
        //rafraichi le select picker :
        $('.selectpicker').selectpicker('refresh');
        //valeur par default : aucun :
        $('#contactLivraison').selectpicker('val', 'Aucun');
        }

//fonction qui boucle sur la liste de client et crée les suggestions : 
let create_list_client = function(tableau_client)
{
        //boucle sur le tableau de résultats : 
        for (let index = 0; index < tableau_client.length; index++) 
        {
                //crée un élément de liste avec chaque client correspondant : 
                $text_list = ' <button class=" btn btn-sm btn-link  click-facturation" value="' + tableau_client[index].client__id + '"><b> (' + tableau_client[index].client__id + ') ' + tableau_client[index].client__societe + '  ' + tableau_client[index].client__cp + '-' + tableau_client[index].client__ville + ' </b></button>';
                $("#list_client_facture").append('<li class="list-group-item d-flex justify-content-between align-items-center"> '
                + $text_list + '</li>');
        }
}

//fonction qui boucle sur la liste de client et crée les suggestions pour la facturation : 
let create_list_client_livraison = function (tableau_client) 
{
        //boucle sur le tableau de résultats : 
        for (let index = 0; index < tableau_client.length; index++) 
        {
                //crée un élément de liste avec chaque client correspondant : 
                $text_list = ' <button class=" btn btn-sm btn-link  click-livraison" value="' + tableau_client[index].client__id + '"><b> (' + tableau_client[index].client__id + ') ' + tableau_client[index].client__societe + '  ' + tableau_client[index].client__cp + '-' + tableau_client[index].client__ville + ' </b></button>';
                $("#list_client_livraison").append('<li class="list-group-item d-flex justify-content-between align-items-center"> '
                + $text_list + ' </li>');
        }
}

//fonction qui efface la liste présente dans le modal pour les suggestions de client facturés: 
let delete_liste_faturation = function()
{
        $("#list_client_facture li").remove();
}

//fonction qui efface la liste présente dans le modal pour les suggestions de client facturés: 
let delete_liste_livraison = function () 
{
        $("#list_client_livraison li").remove();
}

//fonction qui ouvre le modal d'alerte en affichant la variable demandée :
let show_alert = function(text)
{
        $('#alert_text').text(text);
        $('#modal_alert').modal('show');
} 

//fonction qui check la présence de client facturé selectionné  et permet la creation de contact pour la societe :
let verifie_post = function()
{
        let client = $('#clientSelect').val();
       
        if (client.length > 4  ) 
        {
                $('#valid_devis').prop('disabled' , false);
                $('#button_crea_contact').removeClass('d-none');
        }
        else 
        {
                $('#valid_devis').prop('disabled', true);
                $('#button_crea_contact').addClass('d-none');
        }
}
//fonction qui rend possible l'accès au boutton de creation de contact livraison : 
let verifie_livraison = function()
{
        let client_livraison = $('#clientLivraison').val();

        if (client_livraison.length > 4 ) 
        {
                $('#button_contact_crea_livraison').removeClass('d-none');
        }
        else 
        {
                $('#button_contact_crea_livraison').addClass('d-none');
        }
}

//appel de la fonction au chargement de la page : 
verifie_post();
verifie_livraison();

//recherche sur la keypress enter dans l'input client :
$('#client_input').on('keypress' , function(e)
{
        //si la clef est un retour chariot : 
        if(e.which == 13) 
        {
                //annule la soumission du formulaire : 
                e.preventDefault();
                let string_recherche = $('#client_input').val();
                //requete ajax qui recupère les résultats de la requete  : 
                $.ajax({
                        type: 'post',
                        url: "Ajax_search_client_devis",
                        data : 
                            {
                                "search" : string_recherche
                            },
                            success: function(data)
                            {
                                //deserialize objet json (tableau d'objets)
                                console.log(data)
                                dataSet = JSON.parse(data);
                                //si la réponse est nulle : 
                                if (dataSet.length < 1) 
                                {
                                        show_alert('Aucun résultat');
                                        delete_liste_faturation();
                                }
                                //si la réponse est unique je selectionne de suite le client  : 
                                else if (dataSet.length == 1)
                                {
                                        delete_liste_faturation();
                                        select_client_facturation(dataSet[0]);
                                }
                                // si la reponse est multiple je propose une selection : 
                                else 
                                {
                                        //si il y a 20 résultats ou + je préviens l'utilisateur d'utiliser des critères plus précis:
                                        if (dataSet.length == 30) 
                                        {
                                                $('#alert_long_facture').text('Trop de résultats: essayer d affiner la recherche ');  
                                        }
                                        else 
                                        {
                                                $('#alert_long_facture').text(' ');
                                        }
                                        //cree la liste client : 
                                        delete_liste_faturation();
                                        create_list_client(dataSet);
                                        //attribue la fonction click a chaque button créé afin de selectionner le client dynamiquement : 
                                        $('.click-facturation').on('click' , function()
                                        {
                                                delete_liste_faturation();
                                                let client = parseInt(this.value);
                                               //boucle dans le tableau de resultat pour retrouver la concordance: 
                                                for (let index = 0; index < dataSet.length; index++) 
                                               {
                                                        if (dataSet[index].client__id == client) 
                                                        {
                                                                select_client_facturation(dataSet[index]);
                                                        }     
                                               }
                                               //ferme le modal : 
                                                $('#modal_choix_client').modal('hide');
                                        })
                                        //j'ouvre le modal : 
                                        $('#modal_choix_client').modal('show');
                                }
                            },
                            error: function (err) 
                            {
                                console.log('error: ' , err);
                            }
                        })
        }
})

//recherhe sur le click du button recherche :  
$('#btn_search_client').on('click' , function()
{
        let string_recherche = $('#client_input').val();
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
                        }
                        //si la réponse est unique je selectionne de suite le client  : 
                        else if (dataSet.length == 1) {
                                delete_liste_faturation();
                                select_client_facturation(dataSet[0]);
                        }
                        // si la reponse est multiple je propose une selection : 
                        else {
                                //si il y a 20 résultats ou + je préviens l'utilisateur d'utiliser des critères plus précis:
                                if (dataSet.length == 30) 
                                {
                                        $('#alert_long_facture').text('Trop de résultats: essayer d affiner la recherche ');
                                }
                                else 
                                {
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
                                                        select_client_facturation(dataSet[index]);
                                                }
                                        }
                                        //ferme le modal : 
                                        $('#modal_choix_client').modal('hide');
                                })
                                //j'ouvre le modal : 
                                $('#modal_choix_client').modal('show');
                        }
                },
                error: function (err) {
                        console.log('error: ', err);
                }
        })
})

//recherche sur la keypress enter dans l'input client livraison 
$('#client_livraison_input').on('keypress', function (e) 
{
        //si la clef est retour chariot : 
        if (e.which == 13) {
                //annule la soumission du formulaire : 
                e.preventDefault();
                let string_recherche = $('#client_livraison_input').val();
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
                                if (dataSet.length < 1) 
                                {
                                        show_alert('Aucun résultat');
                                        delete_liste_livraison();
                                }
                                //si la réponse est unique je selectionne de suite le client  : 
                                else if (dataSet.length == 1) 
                                {
                                        delete_liste_livraison();
                                        select_client_livraison(dataSet[0]);
                                }
                                // si la reponse est multiple je propose une selection : 
                                else 
                                {
                                        //si il y a 20 résultats ou + je préviens l'utilisateur d'utiliser des critères plus précis:
                                        if (dataSet.length == 30) {
                                                $('#alert_long_livraison').text('Trop de résultats: essayer d affiner la recherche ');
                                        }
                                        else {
                                                $('#alert_long_livraison').text(' ');
                                        }
                                        //cree la liste client : 
                                        delete_liste_livraison();
                                        create_list_client_livraison(dataSet);
                                        //attribue la fonction click a chaque button créé afin de selectionner le client dynamiquement : 
                                        $('.click-livraison').on('click', function () {
                                                delete_liste_livraison();
                                                let client = parseInt(this.value);
                                                //boucle dans le tableau de resultat pour retrouver la concordance: 
                                                for (let index = 0; index < dataSet.length; index++) 
                                                {
                                                        if (dataSet[index].client__id == client) {
                                                                select_client_livraison(dataSet[index]);
                                                        }
                                                }
                                                //ferme le modal : 
                                                $('#modal_choix_client_livraison').modal('hide');
                                        })
                                        //j'ouvre le modal : 
                                        $('#modal_choix_client_livraison').modal('show');
                                        }
                                },
                                error: function (err) {
                                        console.log('error: ', err);
                        }
                })
        }
})

//fonction de recherche client livraison sur le click du boutton recherche : 
$('#btn_search_client_livraison').on('click' , function()
{
        let string_recherche = $('#client_livraison_input').val();
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
                                delete_liste_livraison();
                        }
                        //si la réponse est unique je selectionne de suite le client  : 
                        else if (dataSet.length == 1) {
                                delete_liste_livraison();
                                select_client_livraison(dataSet[0]);
                        }
                        // si la reponse est multiple je propose une selection : 
                        else {
                                //si il y a 20 résultats ou + je préviens l'utilisateur d'utiliser des critères plus précis:
                                if (dataSet.length == 30) {
                                        $('#alert_long_livraison').text('Trop de résultats: essayer d affiner la recherche ');
                                }
                                else {
                                        $('#alert_long_livraison').text(' ');
                                }
                                //cree la liste client : 
                                delete_liste_livraison();
                                create_list_client_livraison(dataSet);
                                //attribue la fonction click a chaque button créé afin de selectionner le client dynamiquement : 
                                $('.click-livraison').on('click', function () {
                                        delete_liste_livraison();
                                        let client = parseInt(this.value);
                                        //boucle dans le tableau de resultat pour retrouver la concordance: 
                                        for (let index = 0; index < dataSet.length; index++) {
                                                if (dataSet[index].client__id == client) {
                                                        select_client_livraison(dataSet[index]);
                                                }
                                        }
                                        //ferme le modal : 
                                        $('#modal_choix_client_livraison').modal('hide');
                                })
                                //j'ouvre le modal : 
                                $('#modal_choix_client_livraison').modal('show');
                        }
                },
                error: function (err) {
                        console.log('error: ', err);
                }
        })
})

})