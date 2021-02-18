
class customCard extends HTMLElement 
{
        constructor() 
        {
                // toujours appeller super en premier dans le constructeur : 
                super();
                let value =  this.getAttribute('value');
                value = JSON.parse(value);
                //definition d'une div container pour les 2 elements :
                const container =  document.createElement('div');
                container.setAttribute('class', 'd-flex flex-row');
        }           
}
export {customCard}; 
