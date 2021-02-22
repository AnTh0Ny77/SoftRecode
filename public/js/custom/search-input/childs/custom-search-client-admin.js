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

                super.callback_function = function(data)
                {
                        if (data != false ) 
                        {
                               //si je n'ai qu'un seul résultat : 
                               if (data.length == 1 ) 
                               {
                                       console.log(data);
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
                                        $('#modalSelection').modal('show');
                               }
                        }
                }
                
       
        }
}
 
export {customSearchClientAdmin};