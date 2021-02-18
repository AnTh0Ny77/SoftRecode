

class customButtonQuantite extends HTMLElement 
{
        constructor() 
        {
                // toujours appeller super en premier dans le constructeur : 
                super();
                // valeur 
                let value =  this.getAttribute('value');
                let target_value = this.getAttribute('target');
                //definition du button: 
                const button_minus = document.createElement('button');
                button_minus.setAttribute('class', 'btn  btn-rounded btn-link');
                button_minus.setAttribute('type', 'button');
                button_minus.addEventListener('click', function()
                {
                ajaxSend(value , "ajax_update_quantite_ligne" , 'minus');
                })
                button_minus.innerHTML = '<i class="fas fa-minus"></i>';
                this.append(button_minus);

                //definition de la target
                let target = document.createElement('span');
                target.innerHTML = target_value;
                this.append(target);

                //definition du button + 
                const button_plus = document.createElement('button');
                button_plus.setAttribute('class', 'btn  btn-rounded btn-link');
                button_plus.setAttribute('type', 'button');
                button_plus.addEventListener('click', function()
                {
                ajaxSend(value , "ajax_update_quantite_ligne" , 'plus');
                })
                button_plus.innerHTML = '<i class="fas fa-plus"></i>';
                this.append(button_plus);

                //requete ajax met à jour les totaux :  
                let ajaxTotal = function(tableau_totaux)
                {       
                        document.getElementById('tva_taux').innerHTML = tableau_totaux[1] + ' %';
                        document.getElementById('montant_tva').innerHTML = tableau_totaux[2] + ' €';
                        document.getElementById('total_ht').innerHTML = tableau_totaux[0] + ' €';
                        document.getElementById('total_ttc').innerHTML = tableau_totaux[3] + ' €';
                }

                //requete ajax modifie les quantités : 
                let ajaxSend = function(value , url , operator)
                {
                        //objet xmlhttprequest : 
                        let XHR = new XMLHttpRequest();
                        let formData = new FormData();
                        formData.append('search' , value);
                        formData.append('operator' , operator);
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
                                        let response = JSON.parse(XHR.response); 
                                        target.innerHTML = response.devl_quantite ;
                                        ajaxTotal(response.totaux);
                                }
                        }
                        XHR.open("POST", url );
                        XHR.send(formData);
                }   
        }        
}
//appelle le custom élément dans le dom : 
customElements.define('custom-button', customButtonQuantite );