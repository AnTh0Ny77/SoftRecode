import { customSearchInput} from '../custom-search-input.js';

class customSearchClient extends customSearchInput 
{
        constructor() 
        {
                // toujours appeller super en premier dans le constructeur : 
                super();
                super.callback_function = function(data)
                {
                        if (data != false ) 
                        {
                               console.log(data);
                        }
                }
       
        }
}
 
export {customSearchClient};