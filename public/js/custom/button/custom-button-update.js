class customUpdateButton extends HTMLElement {
        constructor() 
        {
                // toujours appeller super en premier dans le constructeur : 
                super();

                // a l'instart d'un vrai formulaire les methode et action son à passer dans les attribute 
                let url = this.getAttribute('url');
               
                let value  = this.getAttribute('value');
                //le nom du post à recevoir
                let name = this.getAttribute('name');
               
                //le contenu html présent dans le button : 
                let logo = this.getAttribute('logo');

                let target = this.getAttribute('target');
                let select_target =  document.getElementById(target);
                //definition du form container :
                const form = document.createElement('div');
               
                this.append(form);

               

                //creation de linput de type hidden qui reprend la valeur : 
                const hiddenInput = document.createElement('input');
                hiddenInput.setAttribute('type', 'hidden');
                hiddenInput.setAttribute('name', name);
                hiddenInput.setAttribute('value', value);
                form.append(hiddenInput);

                //definition du boutton : 
                const button = document.createElement('button');
                button.setAttribute('type', 'button');
                button.setAttribute('class', 'btn btn-primary btn-primary btn-rounded  mx-2');
                button.innerHTML = logo;
                form.append(button);
                this.test = button;

               
               
                 //fonction destinnée à etre surchargé prend sytématiquement en paramètre la response : 
                 this.callback_function = function(data)
                 {
                         if (data != false ) 
                         {
                                
                         }
                 }
               
                //definition de la fonction ajax de recherche client => appel une fonction de callback qui doit etre définie dans chaque enfant et appelé avec la fonction : 
                let ajax_send = function(uri , callback)
                {
                          let data = value;
                          //objet xmlhttprequest : 
                          let XHR = new XMLHttpRequest();
                          let formData = new FormData();
                          formData.append('update' ,data);
                          formData.append('etat' ,select_target.value);
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
                                          let response = JSON.parse(XHR.response); 
                                          if (callback) 
                                          {
                                                 callback(response)
                                          }
                                  }
                          }
                          XHR.open("POST", uri );
                          XHR.send(formData);
                }

                //definition des écouteur d'évenements : 
                button.addEventListener('click', () => 
                {
                        ajax_send(url,this.callback_function)
                });

        }


}
export {customUpdateButton}; 
