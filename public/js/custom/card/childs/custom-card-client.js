import { customCard} from '../custom-card.js';

class customCardClient extends customCard 
{
        constructor() 
        {
                // toujours appeller super en premier dans le constructeur : 
                super();
                let id_client = document.getElementById(super.value);
        }
}
 
export {customCardClient}; 