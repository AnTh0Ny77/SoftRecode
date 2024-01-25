document.addEventListener('DOMContentLoaded', function()
{
	var calendarEl = document.getElementById('calendar');
	var calendar = new FullCalendar.Calendar(calendarEl, {
		initialView: 'dayGridWeek' , 
		weekends : false ,
		themeSystem: 'bootstrap',
		contentHeight: "auto",
		// eventDisplay: 'list-item',
		buttonText : {
			today: false , 
			dayGridMonth: 'mois',
			dayGridWeek:  'sem', 
			dayGridDay : 'jour'
		},
		headerToolbar : {
			center : 'dayGridDay dayGridWeek dayGridMonth',
			end: 'prev,next'
		},
		displayEventEnd : false ,
		displayEventTime : false ,
		eventClick: function(eventClickInfo ) 
		{
		console.log(eventClickInfo.event._def.extendedProps);

		$("#autor").text(eventClickInfo.event._def.extendedProps.prenom + " " + eventClickInfo.event._def.extendedProps.nom);
		switch (eventClickInfo.event._def.extendedProps.etat) 
		{
			case 'DEM': $('#status').text('En attente de validation'); break;
			case 'VLD': $('#status').text('Demande acceptée'); break;
			case 'REF': $('#status').text('Demande refusée'); break;
			case 'ANL': $('#status').text('Demande annulée'); break;
		}

		switch (eventClickInfo.event._def.extendedProps.motif) 
		{
		case 'CP'  : $('#type').text('Congés payés'); break;
		case 'MLD' : $('#type').text('Arret maladie'); break;
		case 'NP'  : $('#type').text('Non payés'); break;
		case 'INT' : $('#type').text('Intervention'); break;
		case 'TT'  : $('#type').text('Télétravail'); break;
		case 'RCU' : $('#type').text('Récupération'); break;
		}

		console.log(formatterDateModal(eventClickInfo.event._def.extendedProps.start));
		$('#time').text('Du : ' + formatterDateModal(eventClickInfo.event._def.extendedProps.start));
		$('#time2').text('Au : ' + formatterDateModal(eventClickInfo.event._def.extendedProps.stop));
		$('#infoModal').text(eventClickInfo.event._def.extendedProps.info);
		$('#abs__id').val(eventClickInfo.event._def.extendedProps.id);

		if (eventClickInfo.event._def.extendedProps.id_user == $('#annul_user_id').val()) 
		{
			var now = new Date();
			$('#annul_abs_id').val(eventClickInfo.event._def.extendedProps.id);
			$('#annul_forms').removeClass('d-none');
			if (eventClickInfo.event._def.extendedProps.start > now) 
			{ }
		}
		else
		{
			$('#annul_forms').addClass('d-none');
		}

		$('#exampleModalToggle').modal('show');
}
	});
	
	var array_events =  JSON.parse(document.getElementById('planning').value);

	for (const element of array_events) {
		if(memeJour(element.to__out , element.to__in)) {
			// eventDisplay: 'list-item'
			var temp = {
				id : element.to__id , 
				title : premiereLettreMajusculeAvecPoint(element.prenom) + element.nom + ' - '+ element.to__motif + ' (' + element.to__out.substring(11,16) + '-' + element.to__in.substring(11,16) + ')', 
				backgroundColor : returnBackgroundColorMotif(element.to__motif) ,
				borderColor : returnBackgroundColorEtatBorder(  element.to__abs_etat) ,  
				start : traiterDate(element.to__out) , 
				extendedProps: {
					prenom : element.prenom,
					nom: element.nom , 
					motif: element.to__motif , 
					etat: element.to__abs_etat , 
					info : element.to__info , 
					start : element.to__out , 
					stop : element.to__in , 
					id : element.to__id,
					id_user : element.to__user
				}
			};
		}
		else if(!memeJour(element.to__out , element.to__in) && estLendemain(element.to__in , element.to__out ) &&  traiterDateBool(element.to__in)){
		var temp = {
			id : element.to__id , 
			title :  premiereLettreMajusculeAvecPoint(element.prenom)  + element.nom + ' ' +   element.to__info  , 
			backgroundColor : returnBackgroundColorMotif(element.to__motif ) ,
			borderColor : returnBackgroundColorEtatBorder(  element.to__abs_etat) ,  
			start : traiterDate(element.to__out) , 
			extendedProps: {
				prenom : element.prenom,
				nom: element.nom , 
				motif: element.to__motif , 
				etat: element.to__abs_etat , 
				info : element.to__info ,
				start : element.to__out , 
				stop : element.to__in , 
				id : element.to__id, 
				id_user : element.to__user
			}
		};
	}else if(memeJour(element.to__out , element.to__in) && traiterDateBool(element.to__out) && traiterSortieTardive(element.to__in) ){
		  var temp = {
			  id : element.to__id , 
			  title :  premiereLettreMajusculeAvecPoint(element.prenom)  + element.nom + ' ' +   element.to__info  ,
			  backgroundColor : returnBackgroundColorMotif(element.to__motif ) ,
			  borderColor : returnBackgroundColorEtatBorder(  element.to__abs_etat) ,  
			  start : traiterDate(element.to__out) ,
			  end : traiterDateSortie(ajusterDateFin(element.to__in)) , 
			  extendedProps: {
				prenom : element.prenom,
				nom: element.nom , 
				motif: element.to__motif , 
				etat: element.to__abs_etat , 
				info : element.to__info , 
				start : element.to__out , 
				stop : element.to__in , 
				id : element.to__id , 
				id_user : element.to__user
			}
		};
	}else{
		  var temp = {
			  id : element.to__id , 
			  title :  premiereLettreMajusculeAvecPoint(element.prenom)  + element.nom + ' ' +   element.to__info  ,
			  backgroundColor : returnBackgroundColorMotif(element.to__motif) ,
			  borderColor : returnBackgroundColorEtatBorder(element.to__abs_etat) ,  
			  start : traiterDate(element.to__out) ,
			  end : traiterDateSortie(ajusterDateFin(element.to__in)) ,
			  extendedProps: {
				prenom : element.prenom,
				nom: element.nom , 
				motif: element.to__motif , 
				etat: element.to__abs_etat , 
				info : element.to__info , 
				start : element.to__out , 
				stop : element.to__in , 
				id : element.to__id , 
				id_user : element.to__user
			  } 
			 
		  };
	  }
	  calendar.addEvent(temp);
	
	}
   
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

	function formatterDateModal(inputString) {
		const inputDate = new Date(inputString);
		
		// Vérifie si l'heure est 00:00:00
		const isMidnight = inputDate.getHours() === 0 && inputDate.getMinutes() === 0;
	  
		const options = {
		  day: '2-digit',
		  month: '2-digit',
		  year: 'numeric',
		  hour: '2-digit',
		  minute: '2-digit',
		};
	  
		if (isMidnight) {
		  // Si l'heure est 00:00:00, ne renvoie que la date
		  return inputDate.toLocaleDateString('fr-FR', options);
		} else {
		  // Sinon, renvoie la date et l'heure
		  return inputDate.toLocaleDateString('fr-FR', options);
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


	function premiereLettreMajusculeAvecPoint(chaine) {
		// Vérifie si la chaîne est non vide
		if (chaine && typeof chaine === 'string') {
		  // Récupère la première lettre et la transforme en majuscule
		  var premiereLettreMaj = chaine.charAt(0).toUpperCase();
	  
		  // Ajoute un point
		  return premiereLettreMaj + ".";
		} else {
		  // Si la chaîne est vide ou n'est pas une chaîne de caractères, retourne la chaîne originale
		  return "";
		}
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

	function returnBackgroundColorMotif(motif){
		switch (motif)
		{
			case 'CP'  : return '#3788D8';
			case 'MLD' : return '#EF9595';
			case 'NP'  : return '#00B0EE';
			case 'RCU' : return '#DFD3C3';
			case 'INT' : return '#A2C579';
			case 'TT'  : return '#A2C579';
			default    : return '#808080';
		}
	}

	function returnFaMotif(motif){
		switch (motif)
		{
			case 'CP'  : return '<i class="fa-duotone fa-island-tropical"></i>';
			case 'MLD' : return '<i class="fa-duotone fa-stethoscope"></i>';
			case 'NP'  : return '<i class="fa-duotone fa-piggy-bank"></i>';
			case 'RCU' : return '<i class="fa-duotone fa-reply-clock"></i>';
			case 'INT' : return '<i class="fa-duotone fa-suitcase"></i>';
			case 'TT'  : return '<i class="fa-duotone fa-house-laptop"></i>';
			default    : return '<i class="fa-duotone fa-block-question"></i>';
		}
	}

	function returnBackgroundColorEtatBorder(etat){
		switch (etat)
		{
			case 'VLD' : return 'green';
			case 'DEM' : return 'orange';
			case 'REF' : return 'red';
			case 'ANL' : return 'blue';
			default    : return 'black';
		}
	}


	$('#calendar-container').on('click' , function(){
		window.open('http://c.myrecode.fr', '_blank');
	})

});

