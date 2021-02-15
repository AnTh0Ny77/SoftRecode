import { customButtonList} from './custom-button-list.js';



class customButtonListContact extends customButtonList {
        constructor() 
        {
                // toujours appeller super en premier dans le constructeur : 
                super();
                //surchage des proprietes esthétique du parent du button 
                this.button.setAttribute('class', 'btn btn-link btn-rounded mx-2');
                //surcharge de la focntion de callback 
                this.callback_function = function(data)
                {
                       
                }

                

                   
                
        }


}

export { customButtonListContact }; 