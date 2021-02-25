import {customUpdateButton} from '../button/custom-button-update.js';

class customUpdateEtat extends customUpdateButton {
        constructor() 
        {
                // toujours appeller super en premier dans le constructeur : 
                super();
                let modif = this.getAttribute('modif');
                let modif_id =  document.getElementById(modif);
                
                super.callback_function = function(data)
                {
                        modif_id.innerHTML = data.kw__lib;
                }
               
                this.test.setAttribute('class', 'btn  btn-danger btn-rounded  mx-2');
                
        }

}
export {customUpdateEtat}; 
