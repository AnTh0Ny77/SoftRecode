$(document).ready(function () {
    //sur chaque frappe j'effectue une recherche des concordances clients:
    $('#client_input').on('keyup', function () {
        let recherche = $('#client_input').val();
        $('#search_list li').remove();
        // a partir de 1 caractère: 
        if (recherche.length > 1) {
            //requete ajax:
            $.ajax(
                {
                    type: 'post',
                    url: "Ajax_search_client",
                    data:
                    {
                        "search": recherche
                    },
                    success: function (data) {
                        $('#supose_contact').html('');
                        dataSet = JSON.parse(data);
                        //cree une suggestion pour chaque concordances trouvée: 
                        for (let index = 0; index < dataSet.length; index++) {
                            $('#search_list').append('<li class="list-group-item list-group-item-action click_option" value="' + dataSet[index].client__id + '"> ' + dataSet[index].client__id
                                + " " + dataSet[index].client__societe + " "
                                + dataSet[index].client__cp
                                + " " + dataSet[index].client__ville
                                + '</li>')
                        }
                        //fonction sur le click pour chaque nouvelle option:
                        $('.click_option').on('click', function () {

                            $('#search_list li').remove();
                            $('#client_input').val('');
                            $('#card_client').removeClass('d-none');
                            $('#container_forms').removeClass('d-none');
                            $('#search_client').addClass('d-none');
                            $('#card_form').removeClass('d-none');
                            $('#hidden_client').val(this.value);
                            $('#hidden_client_2').val(this.value);
                            //requete ajax secondaire recupère les infos clients principal : 
                            $.ajax(
                                {
                                    type: 'post',
                                    url: "choixLivraison",
                                    data: { "AjaxLivraison": this.value },
                                    success: function (data) {
                                        dataSet = JSON.parse(data);
                                        $('#titre_client').text(dataSet.client__id + " " + dataSet.client__societe);
                                        $('#societe_client').text(dataSet.client__adr1 + " " + dataSet.client__cp + " - " + dataSet.client__ville);
                                    },
                                    error: function (err) {
                                        console.log('erreur dans la requete secondaire URL : choixLivraison ', err);
                                    }
                                })
                            //requete ajax secondaire recupère les contacts:
                            $.ajax(
                                {
                                    type: 'post',
                                    url: "tableContact",
                                    data: { "AjaxContactTable": this.value },
                                    success: function (data) {
                                        dataSet = JSON.parse(data);

                                        for (let index_contact = 0; index_contact < dataSet.length; index_contact++) {

                                            $('#supose_contact').append('<li class="list-group-item d-flex justify-content-between">' + dataSet[index_contact].contact__nom + " " + dataSet[index_contact].contact__prenom + " " + dataSet[index_contact].kw__lib + ' <button value="' + dataSet[index_contact].contact__id + '" type="button"  class="btn btn-link btn-sm btn_click">Modifier</button> </li>')

                                        }
                                        click_to_modif();
                                    },
                                    error: function (err) {
                                        console.log('erreur dans la requete secondaire URL : tableContact ', err);
                                    }
                                })

                        })
                    },
                    error: function (err) {
                        console.log('erreur dans la requete primaire URL:  Ajax_search_client', err);
                    }
                })
        }
        else {
            $('#search_list li').remove();
        }
    })


    //fonction pour retablir son choix client et reinitialiser le formulaire: 
    $('#client_button').on('click', function () {
        $("#progress_bar").css({ "width": "10%" });
        $('#supose_contact').html('');
        $('#search_list li').remove();
        $('#client_input').val('');
        $('#card_client').addClass('d-none');
        $('#search_client').removeClass('d-none');
        $('#container_forms').addClass('d-none');
        $('#contact_select'); val('');
        $('#card_form').addClass('d-none');
        $('#hidden_client').val('');
        $('#hidden_client_2').val('');

    })

    //attribut la fonction de click au boutton génrés dynamiquement :
    let click_to_modif = function () {
        $('.btn_click').on('click', function () {
            $('#contact_select').val(this.value);
            $('#container_forms').submit();

        })
    }

    $('#post_create').on('click', function () {
        $('#crea_forms').submit();
    })


})


