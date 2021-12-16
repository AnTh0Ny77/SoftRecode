
class customSearchMenu extends HTMLElement 
{
        constructor() 
        {
                // toujours appeller super en premier dans le constructeur : 
                super();

                // a l'instart d'un vrai formulaire les methode et action son à passer dans les attribute 
                let url = this.getAttribute('url');
                let method = this.getAttribute('method');
                //definition du form :
                const form = document.createElement('form');
                form.setAttribute('action', url);
                form.setAttribute('Method', method);
                this.append(form);
                //definition du container : 
                const formRow = document.createElement('div');
                formRow.setAttribute('class', 'd-flex flex-row mx-5');
                form.append(formRow);

                //definition de l'input de recherche : 
                const input_search = document.createElement('input');
                input_search.setAttribute('type', 'search');
                input_search.setAttribute('class', 'form-control form-control-sm border rounded  input-recode');
                input_search.setAttribute('name', 'search');
                formRow.append(input_search);

                //definition du boutton : 
                const button = document.createElement('button');
                button.setAttribute('type', 'button');
                button.setAttribute('class', 'btn btn-primary btn-success btn-sm btn-rounded mx-2');
                button.innerHTML = '<i class="fas fa-search"></i>';
                formRow.append(button);

                //fonction de controle des donnée avant post: 
                let check_form = function () 
                {
                        let data = input_search.value;
                        if (data.length > 1 ) 
                        {
                                form.submit(); 
                        }
                        else input_search.setAttribute('class', 'form-control border input-recode  border-danger');
                }

                //definition des écouteur d'évenements : 
                button.addEventListener('click', () => 
                {
                       check_form();
                });
                input_search.addEventListener('keypress', (e) => {
                        //si la clef est un retour chariot : 
                        if (e.key == "Enter") 
                        {
                                //annule la soumission du formulaire : 
                                e.preventDefault();
                                check_form();
                        }
                })
                input_search.addEventListener('focusout', () =>
                {
                        input_search.setAttribute('class', 'form-control form-control-sm border input-recode rounded');
                })
        }


}
export { customSearchMenu }; 
