import { customSearchInput} from '../custom-search-input.js';

class customSearchClientAdmin extends customSearchInput 
{
        constructor() 
        {
                // toujours appeller super en premier dans le constructeur : 
                super();
                let list =  this.getAttribute('list');
                let target = this.getAttribute('target');
                let text_card = this.getAttribute('textCard');
                let select = this.getAttribute('select');
                let select_target =  document.getElementById(select);
                let text_visio = document.getElementById(text_card);
                let target_hidden = document.getElementById(target);
                let list_id = document.getElementById(list);

                super.callback_function = function(data)
                {
                        if (data != false ) 
                        {
                               //si je n'ai qu'un seul résultat : 
                               if (data.length == 1 ) 
                               {
                                       
                                       text_visio.innerHTML =
                                       "<i class=\"fas fa-check\"></i> (<b>" + data[0].client__id + "</b>) " + data[0].client__societe +" " + data[0].client__cp + " " + data[0].client__ville; 
                                       select_target.innerHTML = '';
                                       select_target.append(new Option('Aucun' , 'Aucun' , true));
                                       target_hidden.value = data[0].client__id;
                                       for (const contact of data[0].contact_list) 
                                       {
                                                select_target.append(new Option(contact.contact__nom +  ' ' +  contact.contact__prenom + ' ' + contact.kw__lib , contact.contact__id , false));
                                       }
                               }
                               //si j'en ai plusieurs : 
                               else 
                               {
                                        for (const client of data) 
                                        {
                                                let list_item = document.createElement('li');
                                                list_item.setAttribute('class', 'list-group-item');
                                                let button_click = document.createElement('button');
                                                button_click.setAttribute('class', 'btn btn-link btn-sm click_suggest');
                                                button_click.setAttribute('value', JSON.stringify(client.contact_list));
                                                button_click.innerHTML = ' <b>(' + client.client__id + ') ' + client.client__societe + '</b> ' + client.client__cp + " " + client.client__ville ;
                                                list_item.append(button_click);
                                                list_id.append(list_item);
                                                
                                                button_click.addEventListener('click' , function()
                                                {
                                                        let data_contact = JSON.parse(this.value);
                                                        text_visio.innerHTML =
                                                                "<i class=\"fas fa-check\"></i> (<b>" + client.client__id + "</b>) " + client.client__societe + " " + client.client__cp + " " + client.client__ville; 
                                                        target_hidden.value = client.client__id;
                                                        select_target.innerHTML = '';
                                                        select_target.append(new Option('Aucun', 'Aucun', true));
                                                        for (const contact of data_contact) 
                                                        {
                                                         select_target.append(new Option(contact.contact__nom + ' ' + contact.contact__prenom + ' ' + contact.kw__lib, contact.contact__id, false));
                                                        }
                                                        list_id.innerHTML = '';
                                                        $('#modalSelection').modal('hide');
                                                })

                                        }

                                        $('#modalSelection').modal('show');
                               }
                        }
                }
                
       
        }
}
 
export {customSearchClientAdmin};