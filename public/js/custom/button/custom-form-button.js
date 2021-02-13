
class customFormButton extends HTMLElement {
        constructor() 
        {
                // toujours appeller super en premier dans le constructeur : 
                super();

                // a l'instart d'un vrai formulaire les methode et action son à passer dans les attribute 
                let url = this.getAttribute('url');
                let method = this.getAttribute('method');
                //la valeur a poster 
                let value  = this.getAttribute('value');
                //le nom du post à recevoir
                let name = this.getAttribute('name');
                //le contenu html présent dans le button : 
                let logo = this.getAttribute('logo');
                //definition du form container :
                const form = document.createElement('form');
                form.setAttribute('action', url);
                form.setAttribute('Method', method);
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

                //fonction de controle des donnée avant post: 
                let check_form = function () 
                {
                        let data = hiddenInput.value;
                        if (data.length > 1) 
                        {
                                form.submit();
                        }
                }
                //definition des écouteur d'évenements : 
                button.addEventListener('click', () => {
                        check_form();
                });
        }


}
export {customFormButton}; 
