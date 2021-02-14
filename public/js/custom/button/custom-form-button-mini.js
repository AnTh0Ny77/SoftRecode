import { customFormButton } from './custom-form-button.js';


class customFormButtonMini extends customFormButton 
{
        constructor() 
        {
                // toujours appeller super en premier dans le constructeur : 
                super();
                this.test.setAttribute('class', 'btn btn-link  btn-rounded  mx-2');
        }
}

export { customFormButtonMini }; 