
// fonction ajout de ligne de Devis : 
/// 1er param -> table 
/// 2nd param -> count pour ID 
/// ensuite  -> la valeur des inputs correspondant à la désignation des parametres 
let addOne = function(table,count,prestation,designation,comClient, comInterne , etat, garantie , xtendAdd , quantite , prix, prixBarre , pn , id__fmm ,textEtat , textPresta  ){
    let row = [];
    row.push(count);
    row.push(textPresta);

    if ( comClient.length > 0 && comInterne.length > 0 ) {

        if (pn.length > 0) {
            row.push(designation + "<br>" + pn +  "<br>  <hr>" + '<b>Commentaire : </b>' + comClient  + '<br> <b>Commentaire interne</b> : ' + comInterne )
        }
        else{
            row.push(designation +  "<br>  <hr>" + '<b>Commentaire : </b>' + comClient  + '<br> <b>Commentaire interne</b> : ' + comInterne )
        }
 
    } 

    else if(comClient.length > 0 && comInterne.length < 1 ){

        if (pn.length > 0) {
            row.push(designation + "<br>" + pn +  "<br>  <hr>" + '<b>Commentaire : </b>' + comClient);
        }

        else{
            row.push(designation + "<br>  <hr>" + '<b>Commentaire : </b>' + comClient);
        }

       
    }

    else if(comInterne.length > 0 && comClient.length < 1 ){

        if (pn.length > 0) {
            row.push(designation + "<br>" + pn +  "<br> <hr>" + '<b>Commentaire interne</b> :' + comInterne);
        }
        else {
            row.push(designation  +  "<br> <hr>" + '<b>Commentaire interne</b> :' + comInterne);
        }
       
    }
    else {
        if (pn.length > 0) {
            row.push(designation + "<br>" + pn );
        }
        else{
            row.push(designation);
        }
       
    }
    row.push(textEtat);
    if (xtendAdd.length > 0) {
        let element;
        let xtendString = "";
        for (let index = 0; index < xtendAdd.length; index++) {
            element  =  xtendAdd[index][0] + ' mois ' + +xtendAdd[index][1] + ' €<br>';
            xtendString += element;
        }
            if (garantie > 0) {
                row.push( garantie + " mois" + " <hr> <b>Extensions</b> : <br>" + xtendString);  
            }else {  row.push("sans garantie"  +  " <hr> <b>Extensions</b> : <br>" + xtendString )  }
          
    } else {
        if (garantie != "00") {
            row.push( garantie + " mois"); 
        }else {  row.push("sans garantie"  )  }
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
    rowObject.quantite =  quantite;
    rowObject.pn = pn ;
    rowObject.prix = prix;
    rowObject.prixBarre = prixBarre;
    rowObject.id__fmm = id__fmm;
   

    row.push(rowObject);
    table.row.add(row).draw( false );
    row = [];
    $('#referenceS').val(designation);
   
}



let modifyLine = function (table,id,prestation,designation,comClient, comInterne , etat, garantie , xtendAdd , quantite , prix, prixBarre ,pn , id__fmm  , textEtat ,textPresta) {
        let row = [];
        row.push(id);
        row.push(textPresta);
        if ( comClient.length > 0 && comInterne.length > 0 ) {

            if (pn.length > 0) {
                row.push(designation + "<br>" + pn +  "<br>  <hr>" + '<b>Commentaire : </b>' + comClient  + '<br> <b>Commentaire interne</b> : ' + comInterne )
            }
            else{
                row.push(designation +  "<br>  <hr>" + '<b>Commentaire : </b>' + comClient  + '<br> <b>Commentaire interne</b> : ' + comInterne )
            }
     
        } 
    
        else if(comClient.length > 0 && comInterne.length < 1 ){
    
            if (pn.length > 0) {
                row.push(designation + "<br>" + pn +  "<br>  <hr>" + '<b>Commentaire : </b>' + comClient);
            }
    
            else{
                row.push(designation + "<br>  <hr>" + '<b>Commentaire : </b>' + comClient);
            }
    
           
        }
    
        else if(comInterne.length > 0 && comClient.length < 1 ){
    
            if (pn.length > 0) {
                row.push(designation + "<br>" + pn +  "<br> <hr>" + '<b>Commentaire interne</b> :' + comInterne);
            }
            else {
                row.push(designation  +  "<br> <hr>" + '<b>Commentaire interne</b> :' + comInterne);
            }
           
        }
        else {
            if (pn.length > 0) {
                row.push(designation + "<br>" + pn );
            }
            else{
                row.push(designation);
            }
           
        }
        row.push(textEtat);
        if (xtendAdd.length > 0) {
            let element;
            let xtendString = "";
            for (let index = 0; index < xtendAdd.length; index++) {
                element  =  xtendAdd[index][0] + ' mois ' + +xtendAdd[index][1] + ' €<br>';
                xtendString += element;
            }
                if (garantie != "00") {
                    row.push( garantie + " mois" + " <hr> <b>Extensions</b> : <br>" + xtendString);  
                }else {  row.push("sans garantie"  +  " <hr> <b>Extensions</b> : <br>" + xtendString )  }
            
        } else {
            if (garantie != "00") {
                row.push( garantie + " mois"); 
            }else {  row.push("sans garantie"  )  }
        
            
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
        rowObject.pn = pn ;
        rowObject.xtend = xtendAdd;
        rowObject.quantite = quantite;
        rowObject.prix = prix;
        rowObject.prixBarre = prixBarre;
        rowObject.id__fmm = id__fmm;
       

        row.push(rowObject);
        table.row('.selected').data( row ).draw( false );
    }



// function qui check le nombre de ligne de la table et rend possible l'export :
let checkTableRows = function(subject){
    lines = subject.data().count();
    if (lines < 1) {
        $('#xPortData').prop("disabled", true);
    }else{
        $('#xPortData').removeAttr('disabled');
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
      let text = data.contact__nom + '<br>' + data.contact__prenom + '<br>' + data.kw__lib ;
      return text; 
    }


    //function du menu navigation dans mes devis : 
    let Nav = function (button , show , hide , hide2 ){
        $(button).on('click', function(){
            $(hide).hide();
            $(hide2).hide();

            $(show).show();
        })

    }


    // function qui renvoi le selected dans le text approprié  :  
    let selectToText = function(select , text ){
        $(select).on('change' , function(){
          var selectedOption = $(this).children("option:selected").text();
          $(text).val(selectedOption);
        })
    }


    








    


    