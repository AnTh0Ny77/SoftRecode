

class customSpanActive extends HTMLElement 
{
        constructor() 
        {
                // toujours appeller super en premier dans le constructeur : 
                super();
                // valeur 
                let value =  this.getAttribute('value');
                //definition du span: 
                const span = document.createElement('span');
                span.setAttribute('style', 'color: LimeGreen;');
                span.innerHTML = '<i class="fas fa-toggle-on"></i>';
                
                //ecoute evènement click : 
                span.addEventListener('click', function()
                {
                        ajaxSend(value);
                })
                
                this.append(span);

                //requete ajax modifie les quantités : 
                let ajaxSend = function(value )
                {
                        //objet xmlhttprequest : 
                        let XHR = new XMLHttpRequest();
                        let formData = new FormData();
                        formData.append('ligne__id' , value);
                       
                        //requete ajax qui recupère les résultats de la requete : 
                        XHR.onerror = function()
                        {
                                console.log('erreur')
                        }
                        //fonction qui ecoute le changement de status si à 4 je renvoi la réponse : 
                        XHR.onreadystatechange = function()
                        {
                                if (XHR.readyState === 4) 
                                {
                                        console.log(XHR.response);     
                                }
                        }
                        XHR.open("POST", url );
                        XHR.send(formData);
                }   
        }        
}
//appelle le custom élément dans le dom : 
customElements.define('custom-span', customSpanActive );