$(document).ready(function() 
{

//recherche sur la keypress enter dans l'input client 
$('#client_input').on('keypress' , function(e)
{
        //si la clef est retour chariot : 
        if(e.which == 13) 
        {
                //annule la soumission du formulaire : 
                e.preventDefault();
                let string_recherche = $('#client_input').val();
                //requete ajax qui recupère les résultats de la requete  : 
                $.ajax({
                        type: 'post',
                        url: "Ajax_search_client",
                        data : 
                            {
                                "search" : string_recherche
                            },
                            success: function(data)
                            {
                                //deserialize objet json (tableau d'objets)
                                dataSet = JSON.parse(data);
                                //si la réponse est nulle : 
                                if (dataSet.length < 1) 
                                {
                                        alert('aucunne réponse');
                                }
                                //si la réponse est unique je selectionne de suite le client  : 
                                else if (dataSet.length == 1)
                                {
                                        console.log(dataSet[0]);
                                        //cosmetique rouge/vert : 
                                        $('#span_facturation').removeClass('text-danger');
                                        $('#span_facturation').addClass('text-success');
                                        //indication visuelle pour l'utilisateur : 
                                        $('#span_facturation').html( 
                                        '<b> ('+ dataSet[0].client__id +') '+ dataSet[0].client__societe + '  ' + dataSet[0].client__cp +  '-' +  dataSet[0].client__ville + ' </b> <i class="fas fa-check"></i>' );
                                        //gestion des contacts : 
                                        $('#contactSelect option').remove();
                                        //option pas de contacts : 
                                        $('#contactSelect').append(new Option('Aucun', 'Aucun' , false, true));
                                                //boucle sur la liste des contacts présents et crée les options adéquetes : 
                                                for (let index = 0; index < dataSet[0].contact_list.length; index++)
                                                {
                                                        $('#contactSelect').append(
                                                        new Option(dataSet[0].contact_list[index].contact__nom 
                                                        + " " +dataSet[0].contact_list[index].contact__prenom + " - "  
                                                        + dataSet[0].contact_list[index].kw__lib ,dataSet[0].contact_list[index].contact__id));  
                                                }
                                                $('.selectpicker').selectpicker('refresh'); 
                                                $('#contactSelect').selectpicker('val', 'Aucun');
                                }
                                // si la reponse est multiple je propose une selection : 
                                else 
                                {
                                        alert('reponse multiple');
                                }
                            },
                            error: function (err) 
                            {
                                console.log('error: ' , err);
                            }
                        })
        }
})


})