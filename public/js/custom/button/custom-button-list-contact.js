import { customButtonList} from './custom-button-list.js';


class customButtonListContact extends customButtonList {
        constructor() 
        {
                // toujours appeller super en premier dans le constructeur : 
                super();
                //surchage des proprietes esthétique du parent du button :
                this.button.setAttribute('class', 'btn btn-link btn-rounded mx-2');
               
                let list_id = this.getAttribute('target');
                let list = document.getElementById(list_id);
              
                //surcharge de la focntion de callback 
                this.callback_function = function(data)
                {
                       
                        //supprime la liste existante : 
                        while (list.firstChild) 
                        {
                                list.removeChild(list.firstChild); 
                        }
                        //boucle sur la nouvelle list : 
                        for (let index = 0; index < data.length; index++) 
                        {
                               //pour chaque client uen ligne : 
                               let row = document.createElement('tr');

                               //premiere cellule 
                               let premiere_cellule = document.createElement('td');
                               let paragraphe = document.createElement('p');
                               //civilité nom prénom
                               let contenu_text = ' <b> '+ data[index].contact__civ + ' ' +  data[index].contact__nom +' '+ data[index].contact__prenom +'</b>'
                               premiere_cellule.append(paragraphe);
                               let span = document.createElement('span');
                               span.setAttribute('class' , 'mx-2 text-muted font-italic');
                               span.innerHTML = data[index].kw__lib ; 
                               paragraphe.innerHTML = contenu_text + ' '  ;
                               paragraphe.append(span);
                               row.append(premiere_cellule);
                               
                               //deuxième cellule : 
                               let deuxieme_cellule = document.createElement('td');
                               //telephone fixe : 
                               let paragraphe_second = document.createElement('p');
                               deuxieme_cellule.append(paragraphe_second);
                               let h6 = document.createElement('h6');
                               paragraphe_second.append(h6);
                               let span_1 = document.createElement('span');
                               span_1.setAttribute('class' , 'mx-2');
                               //si le telephone client fixe existe : 
                               let content_telephone = '<small>Non-renseigné</small>';
                               if (data[index].contact__telephone) 
                               {
                                         content_telephone = data[index].contact__telephone;
                               }
                               span_1.innerHTML = '<i class="far fa-phone-office"></i> : <b> ' + content_telephone + '</b>';
                               h6.append(span_1);
                               //telephone portable : 
                               let span_2 = document.createElement('span');
                               span_2.setAttribute('class' , 'mx-2');
                               //si le telephone client portable existe : 
                               let content_gsm = '<small>Non-renseigné</small>';
                               if (data[index].contact__gsm) 
                               {
                                         content_gsm = data[index].contact__gsm;
                               }
                               span_2.innerHTML = '<i class="fas fa-mobile-android-alt"></i> : <b> ' + content_gsm + '</b>';
                               h6.append(span_2);
                               //adresse e-mail: 
                               let span_3 = document.createElement('span');
                               span_3.setAttribute('class' , 'mx-2');
                               //si le mail client existe :  
                               let content_email = '<i class="far fa-envelope-open-text"></i> : <small>Non-renseigné</small>';
                               if (data[index].contact__email) 
                               {
                                 content_email = '<a href="mailto:'+data[index].contact__email+'" class="badge badge-light"><i class="far fa-envelope-open-text"></i> : <b>'+data[index].contact__email+'</b></a>';
                               }
                               span_3.innerHTML = content_email;
                               h6.append(span_3);
                               row.append(deuxieme_cellule);
                               //troisième cellule : 
                               let troisieme_cellule =  document.createElement('td');
                                let button_mini = "<custom-form-button-mini method='POST' url='contactCrea' name='contact_select' value='" + data[index].contact__id + "'logo='<i class=\"far fa-user-edit\"></i>' </custom-form-button-mini>";
                               troisieme_cellule.innerHTML = button_mini;
                               row.append(troisieme_cellule);
                               list.append(row);
                        }
                        console.log(data);
                      
                }
    
        }


}

export { customButtonListContact }; 