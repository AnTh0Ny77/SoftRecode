

class customSearchInput extends HTMLElement 
{
        constructor() 
        {
                // toujours appeller super en premier dans le constructeur : 
                super();
                // valeur 
                let url =  this.getAttribute('url');
                let target_value = this.getAttribute('target');

                //definition d'une div container pour les 2 elements :
                const container =  document.createElement('div');
                container.setAttribute('class', 'd-flex flex-row');
                //definition de l'input :
                const input_search = document.createElement('input');
                input_search.setAttribute('type', 'search');
                input_search.setAttribute('class', 'form-control');
                
                //definition du boutton : 
                const button = document.createElement('button');
                button.setAttribute('type', 'button');
                button.setAttribute('class', 'btn btn-primary btn-primary btn-rounded mx-2');
                button.innerHTML = '<i class="fas fa-search"></i>';
                //on utilise append pour attribuer les éléments a notre custom élément : 
                this.append(container);
                container.append(input_search , button );


                //definition de la fonction ajax de recherche client : 
                this.ajax_send = function(uri , callback)
                {
                         let data = this.input_search.getAttribute('value');
                         //objet xmlhttprequest : 
                         let XHR = new XMLHttpRequest();
                         let formData = new FormData();
                         formData.append('search' ,data);
                         //requete ajax qui recupère les résultats de la requete : 
                         XHR.onerror = function()
                         {
                                 console.log('erreur : ', error)
                         }
                         //fonction qui ecoute le changement de status si à 4 je renvoi la réponse : 
                         XHR.onreadystatechange = function()
                         {
                                 if (XHR.readyState === 4) 
                                 {
                                         console.log(XHR.response);
                                         callback();
                                 }
                         }
                         XHR.open("POST", uri );
                         XHR.send(formData);
                }
        }        

       
}
//appelle le custom élément dans le dom : 
customElements.define('custom-search-input', customSearchInput );