//import des customs éléments pret a etre employés :  

//carte d'affichage du client passé en attribut value :
import {customCardClient} from './card/childs/custom-card-client.js';
customElements.define('custom-card-client', customCardClient );

//input de recherche client attribut : url et target à spécifier : 
import {customSearchClient} from './search-input/childs/custom-search-client.js';
customElements.define('custom-search-client', customSearchClient );

//input de recherche client attribut :spécifique a la page d'administration des fiches mais reurtilisable: 
import {customSearchClientAdmin} from './search-input/childs/custom-search-client-admin.js';
customElements.define('custom-search-client-admin', customSearchClientAdmin );

//formulaire de recherche utilisé globalement : layout 
import { customSearchMenu } from './search-input/custom-search-menu.js';
customElements.define('custom-search-menu', customSearchMenu);

//boutton formulaire pour envoi rapide data : 
import { customFormButton} from './button/custom-form-button.js';
customElements.define('custom-form-button', customFormButton);

//boutton formulaire mini format pour envoi rapide data : 
import { customFormButtonMini } from './button/custom-form-button-mini.js';
customElements.define('custom-form-button-mini', customFormButtonMini);

//bouton de creation de la list de contact supplémentaire :  
import{customButtonListContact} from './button/custom-button-list-contact.js';
customElements.define('custom-button-list-contact', customButtonListContact);

//button d'update de l'etat s'une commande à partir d'un select : 
import {customUpdateEtat} from "./button/custom-button-update-etat.js";
customElements.define('custom-button-update-etat', customUpdateEtat);












 


