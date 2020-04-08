
// fonction ajout de ligne de Devis : 
/// 1er param -> table 
/// 2nd param -> count pour ID 
/// ensuite  -> la valeur des inputs correspondant à la désignation des parametres 
let addOne = function(table,count,prestation,designation,comClient, comInterne , etat, garantie , xtendAdd , quantite , prix, prixBarre){
    let row = [];
    row.push(count);
    row.push(prestation);
    if ( comClient.length > 0 && comInterne.length > 0 ) {
        row.push(designation + "<br>  <hr>" + '<b>Commentaire : </b>' + comClient  + '<br> <b>Commentaire interne</b> : ' + comInterne )
    } 
    else if(comClient.length > 0 && comInterne.length < 1 ){
        row.push(designation + "<br>  <hr>" + '<b>Commentaire : </b>' + comClient);
    }
    else if(comInterne.length > 0 && comClient.length < 1 ){
        row.push(designation + "<br> <hr>" + '<b>Commentaire interne</b> :' + comInterne);
    }
    else {
        row.push(designation);
    }
    row.push(etat);
    if (xtendAdd.length > 0) {
        let element;
        let xtendString = "";
        for (let index = 0; index < xtendAdd.length; index++) {
            element  =  xtendAdd[index][0] + ' mois ' + +xtendAdd[index][1] + ' €<br>';
            xtendString += element;
        }
        row.push( garantie + " mois" + " <hr> <b>Extensions</b> : <br>" + xtendString);    
    } else {
        row.push( garantie +'mois');
    }
    row.push( quantite);
    let prixMultiple ;
    if (prixBarre.length > 0) {
        prixMultiple =  ' <s>' + prixBarre  + "€</s> " + prix + " €" ;
    }else {prixMultiple =  prix + " €" ;};
    row.push(prixMultiple);
    let rowObject = new Object();
    rowObject.id = counter;
    rowObject.prestation = prestation;
    rowObject.designation = designation;
    rowObject.comClient = comClient;
    rowObject.comInterne = comInterne;
    rowObject.etat = etat;
    rowObject.garantie = garantie;
    rowObject.xtend = xtendAdd;
    rowObject.quantite = quantite;
    rowObject.prix = prix;
    rowObject.prixBarre = prixBarre;
    row.push(rowObject);
    table.row.add(row).draw( false );
    row = [];
    $('#referenceS').val(designation);
    count ++; 
}



// function qui check le nombre de ligne de la table et rend possible l'export :
let checkTableRows = function(subject){
    lines = subject.data().count();
    if (lines < 1) {
        $('#xportPdf').prop("disabled", true);
    }else{
        $('#xportPdf').removeAttr('disabled');
    }
    }


    let checkClient = function(subject){
        lines = subject.data().count();
        if (lines < 1) {
            $('#addNewRow').prop("disabled", true);
        }else{
            $('#addNewRow').removeAttr('disabled');
        }
        }

    

    // function d'afficchage d'une societe dans le DOM :  

    let showClient = function(data){
      let text =    data.client__societe + ' (' + data.client__id +')<br>' + data.client__adr1 + '<br>' + data.client__cp + " " + data.client__ville;
      return text ;
    }

    // function d'affichage d'un contact : 
    let showContact = function(data){
      let text = data.contact__nom + '<br>' + data.contact__prenom + '<br>' + data.keyword__lib ;
      return text; 
    }