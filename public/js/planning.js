document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');
    var calendar = new FullCalendar.Calendar(calendarEl, {
      initialView: 'dayGridWeek' , 
      weekends : false ,
      themeSystem: 'bootstrap',
      buttonText : {
          today: 'Aujourdhui' , 
          dayGridMonth: 'mois',
          dayGridWeek:  'semaine'
      },
      headerToolbar : {
          center : 'dayGridWeek dayGridMonth',
          end: 'today prev,next'
      },
      
      eventMouseEnter : function(info){
          console.log(info.event.extendedProps );
          $('#presTitle').text(info.event._def.title);
          $('#presDetails').html( '<b>' + info.event.extendedProps.details + '</b>');
          
      },
      eventMouseLeave : function(info){
          $('#presTitle').text('');
          $('#presDetails').html('');
      }
    });

    var array_events = JSON.parse($('#lisTime').val());

    for (const element of array_events) {
      if(memeJour(element.to__out , element.to__in) &&  traiterDateBool(element.to__out) ) {
          var temp = {
              id : element.to__id , 
              title :  element.prenom + ' ' + element.nom + ' ' +   element.to__info  , 
              backgroundColor : returnBackgroundColor(element.to__info) ,
              borderColor : returnBackgroundColor(element.to__info) , 
              extendedProps : {
                  details : 'Absent la journée ' + 'Pour le motif suivant :  <p class="recode-p">'+ element.to__info+'</p>' 
              },
              start : traiterDate(element.to__out) 
          };
      }else if(!memeJour(element.to__out , element.to__in) && estLendemain(element.to__in , element.to__out ) &&  traiterDateBool(element.to__in)){
          var temp = {
              id : element.to__id , 
              title :  element.prenom + ' ' + element.nom + ' ' +   element.to__info  , 
              extendedProps : {
                  details : 'Absent le  <p class="recode-p">' + traiterDate(element.to__out) +
              +  "</p> Pour le motif suivant : <p class='recode-p'>"  +  element.to__info + '</p>'
              },
              backgroundColor : returnBackgroundColor(element.to__info) ,
              borderColor : returnBackgroundColor(element.to__info) , 
              start : traiterDate(element.to__out) 
          };
      }else if(memeJour(element.to__out , element.to__in) && traiterDateBool(element.to__out) && traiterSortieTardive(element.to__in) ){
          var temp = {
              id : element.to__id , 
              title :  element.prenom + ' ' + element.nom + ' ' +   element.to__info  ,
              extendedProps : {
                  details : 'Absent du <p class="recode-p">' + formaterDate(traiterDate(element.to__out)) + ' Au lendemain matin'
              +  "</p> Pour le motif suivant : <p class='recode-p'>"  +  element.to__info + '</p>'
              },
              backgroundColor : returnBackgroundColor(element.to__info) ,
              borderColor : returnBackgroundColor(element.to__info) , 
              start : traiterDate(element.to__out) ,
              end : traiterDateSortie(ajusterDateFin(element.to__in))
          };
      }else{
         //si c'est pas le lendemain 
        
          var temp = {
              id : element.to__id , 
              title :  element.prenom + ' ' + element.nom + ' ' +   element.to__info  ,
              extendedProps : {
                  details : 'Absent du <p class="recode-p">' + formaterDate(traiterDate(element.to__out)) + '</p> Au <p class="recode-p">' 
                      +  formaterDate(traiterDateSortie(ajusterDateFin(element.to__in))) 
              +  "</p> Pour le motif suivant : <p class='recode-p'>"  +  element.to__info + '</p>'
              },
              backgroundColor : returnBackgroundColor(element.to__info) ,
              borderColor : returnBackgroundColor(element.to__info) , 
              start : traiterDate(element.to__out) ,
              end : traiterDateSortie(ajusterDateFin(element.to__in))
          };
      }
      calendar.addEvent(temp);
    }
    
    calendar.setOption('height', 700);
    calendar.setOption('locale', 'fr');
    calendar.render();

  function traiterDate(dateSQL) {
      // Convertir la date SQL en objet Date
      var date = new Date(dateSQL);
      // Vérifier si l'heure est inférieure à 9h du matin
      if (date.getHours() < 9 || date.getHours() == 9 ) {
          // Supprimer la précision heure minute seconde
         
          // Retourner la nouvelle chaîne au format ISO (YYYY-MM-DD)
          return date.toISOString().split('T')[0];
      } else {
          // Si l'heure n'est pas inférieure à 9h du matin, retourner la date originale
          return dateSQL;
      }
  }

  function formaterDate(date) {
      var dateObj;
  
      // Vérifier le format de la date
      if (date.includes(' ')) {
          // Si la date contient un espace, c'est probablement le format "2023-10-05 13:30:00"
          dateObj = new Date(date);
      } else {
          // Sinon, c'est probablement le format "2023-09-25"
          var composantsDate = date.split('-');
          dateObj = new Date(composantsDate[0], composantsDate[1] - 1, composantsDate[2]);
      }
  
      // Si l'heure est définie, construire la chaîne avec l'heure
      if (!isNaN(dateObj.getTime())) {
          var jour = dateObj.getDate();
          var mois = moisEnLettres(dateObj.getMonth() + 1); // Les mois commencent à 0 dans les objets Date
          var annee = dateObj.getFullYear();
          var heure = dateObj.getHours();
          var minutes = dateObj.getMinutes();
          var secondes = dateObj.getSeconds();
  
          // Vérifier si l'heure, les minutes et les secondes sont tous égaux à zéro
          var heureNonNulle = heure !== 0 || minutes !== 0 || secondes !== 0;
  
          // Construire la chaîne formatée
          var dateFormatee = jour + ' ' + mois + ' ' + annee;
  
          // Ajouter l'heure et les minutes si disponibles
          if (heureNonNulle) {
              dateFormatee += ' à ' + (heure < 10 ? '0' : '') + heure + 'h' + (minutes < 10 ? '0' : '') + minutes;
          }
  
          return dateFormatee;
      }
  
      // Si l'heure n'est pas définie, utiliser le format d'origine
      return date;
  }
  



// Fonction pour convertir le mois de chiffres à toutes lettres
function moisEnLettres(moisNumerique) {
  var moisEnAnglais = [
      "janvier", "février", "mars", "avril", "mai", "juin",
      "juillet", "août", "septembre", "octobre", "novembre", "décembre"
  ];

  // Récupérer le mois en toutes lettres à partir du tableau
  return moisEnAnglais[parseInt(moisNumerique, 10) - 1];
}

  function extraireHeureMinutesFormat(date) {
      // Convertir la date en objet Date
      var dateObj = new Date(date);
  
      // Extraire l'heure et les minutes
      var heures = dateObj.getHours();
      var minutes = dateObj.getMinutes();
  
      // Formater l'heure et les minutes
      var heureMinutesFormat = heures.toString().padStart(2, '0') + 'h ' +
                               minutes.toString().padStart(2, '0');
  
      // Retourner la chaîne formatée
      return heureMinutesFormat;
  }


  function ajusterDateFin(dateFin) {
      // Convertir la date de fin en objet Date
      var date = new Date(dateFin);
  
      // Vérifier si l'heure de fin est inférieure à 9h15 du matin
      if (date.getHours() < 9 || (date.getHours() === 9 && date.getMinutes() < 15)) {
          // Remettre la date à la veille
          date.setDate(date.getDate() - 1);
          // Retourner la nouvelle date au format "YYYY-MM-DD"
          return date.toISOString().split('T')[0];
      } else {
          // Si l'heure de fin n'est pas inférieure à 9h15 du matin, retourner la date originale
          return dateFin;
      }
  }


  function formaterDateSansHeure(dateDebut) {
      // Convertir la date en objet Date
      var date = new Date(dateDebut);
  
      // Vérifier si l'heure de début est minuit
      if (date.getHours() === 0 && date.getMinutes() === 0 && date.getSeconds() === 0) {
          // Retourner la date au format "YYYY-MM-DD"
          return date.toISOString().split('T')[0];
      } else {
          // Si l'heure de début n'est pas minuit, retourner la date originale
          return dateDebut;
      }
  }

  function estLendemain(date1, date2) {
      // Convertir les dates en objets Date
      var d1 = new Date(date1);
      var d2 = new Date(date2);
  
      // Ajouter un jour à la première date
      d1.setDate(d1.getDate() + 1);
  
      // Comparer les années, les mois et les jours
      return d1.getFullYear() === d2.getFullYear() &&
             d1.getMonth() === d2.getMonth() &&
             d1.getDate() === d2.getDate();
  }

  function traiterDateBool(dateSQL) {
      // Convertir la date SQL en objet Date
      var date = new Date(dateSQL);
      // Vérifier si l'heure est inférieure à 9h du matin
      if (date.getHours() < 9 || date.getHours() == 9 ) {
          // Supprimer la précision heure minute seconde
         
          // Retourner la nouvelle chaîne au format ISO (YYYY-MM-DD)
          return true
      } else {
          // Si l'heure n'est pas inférieure à 9h du matin, retourner la date originale
          return false;
      }
  }

  function traiterSortieTardive(dateSQL) {
      // Convertir la date SQL en objet Date
      var date = new Date(dateSQL);
      // Vérifier si l'heure est inférieure à 9h du matin
      if (date.getHours() > 18 || date.getHours() == 18 ) {
          // Supprimer la précision heure minute seconde
         
          // Retourner la nouvelle chaîne au format ISO (YYYY-MM-DD)
          return true
      } else {
          // Si l'heure n'est pas inférieure à 9h du matin, retourner la date originale
          return false;
      }
  }

  function memeJour(date1, date2) {
      // Convertir les dates en objets Date
      var d1 = new Date(date1);
      var d2 = new Date(date2);
  
      // Comparer l'année, le mois et le jour
      return d1.getFullYear() === d2.getFullYear() &&
             d1.getMonth() === d2.getMonth() &&
             d1.getDate() === d2.getDate();
  }

  function traiterDateSortie(dateSQL){
      // Convertir la date SQL en objet Date
      var date = new Date(dateSQL);
      // Vérifier si l'heure est inférieure à 9h du matin
      if (date.getHours() > 17 || (date.getHours() === 17 && date.getMinutes() > 30) ) {
          // Retourner la nouvelle chaîne au format ISO (YYYY-MM-DD)
          return date.toISOString().split('T')[0];
      } else {
          // Si l'heure n'est pas inférieure à 9h du matin, retourner la date originale
          return dateSQL;
      }
  }

  function comparerString(str, tableauComparaison) {
      // Convertir le string en majuscules pour la comparaison insensible à la casse
      var strMajuscules = str.toUpperCase();
  
      // Parcourir le tableau de comparaison
      for (var i = 0; i < tableauComparaison.length; i++) {
          // Convertir la chaîne actuelle du tableau en majuscules pour la comparaison
          var comparaisonActuelle = tableauComparaison[i].toUpperCase();
  
          // Vérifier s'il y a une correspondance
          if (strMajuscules === comparaisonActuelle) {
              // Retourner une réponse spécifique à la correspondance trouvée
              return true ;
          }
      }
      // Retourner une réponse par défaut si aucune correspondance n'est trouvée
      return false;
  }

  function verifierCP(chaine) {
      // Extraire les deux premiers caractères de la chaîne et les convertir en majuscules
      var deuxPremiersCaracteres = chaine.substring(0, 2).toUpperCase();
  
      // Vérifier si les deux premiers caractères sont égaux à "CP"
      return deuxPremiersCaracteres === "CP";
  }

  function returnBackgroundColor(string){
      if(comparerString(string,['cp' , 'conge' , 'congé' , 'conges' , 'congés' , 'vacance' , 'vacances' , 'cong'])){
          return '#61A3BA';
      }
      if(comparerString(string,['malade' , 'malad' , 'medic' , 'medical' , 'rdv m' , 'rdv medical' , 'rdv medic' ])){
          return '#EF9595';
      }
      if(comparerString(string,['perso' , 'autre' ])){
          return '#A2C579';
      }
      if(verifierCP(string)){
          return '#61A3BA';
      }
      return '#DFD3C3';
  }

  });