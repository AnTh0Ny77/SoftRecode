$(document).ready(function() {


// intialisation de la table des commandes en cours : 
 let commandTableCours = $('#commandCoursTable').DataTable({
    "paging": true,
    "info": true,
    retrieve: true,
    "deferRender": true,
    "searching": true,
    "columnDefs": [{
    "targets": [ 5 ],
    "visible": false,
    "searchable": false
},]});

// initialisation de la table saisie :
let saisieTable = $("#saisieTable").DataTable({
    "paging": true,
    "info": true,
    retrieve: true,
    "deferRender": true,
    "searching": true,
    "columnDefs": [{
        "targets": [ 5 ],
        "visible": false,
        "searchable": false
    },]
});





// function attribut disabled des buttons: 
let checkButtonTravail = function(){
    let Rowtravail =  $('#commandCoursTable').find('tr');
    if (Rowtravail.hasClass('selected')) {
    $('.TravailButton').removeAttr('disabled');
    } else { $('.TravailButton').prop("disabled", true);}
}
checkButtonTravail();

//function selection dans la table commande en cours et attributs des button de post : 
commandTableCours.on('click','tr',function(){
    if ( $(this).hasClass('selected') ){
    $(this).removeClass('selected');}
    else if(commandTableCours.rows().count() >= 1){
    $('#commandCoursTable tr.selected').removeClass('selected');
    $(this).addClass('selected');
    idRow = commandTableCours.row('.selected').data()[0];
    dataRow = commandTableCours.row('.selected').data()[0];
    $('#travailFiche').val(idRow);
    $('#bonLivraison').val(idRow);
    checkButtonTravail();

    // requete Ajax sur la commande selectionnée : 
    $.ajax({
    type: 'post',
    url: "AjaxCMDcours",
    data : 
    {"AjaxCmd" : dataRow},
    success: function(data){
    dataSet = JSON.parse(data);
    $('#AjaxCommande').html(dataSet[0].client__societe + "<br>" + dataSet[0].client__ville + " " + dataSet[0].client__cp );
    if (dataSet[0].contact__nom){
    $('#AjaxContactCMD').html(dataSet[0].contact__nom + " " + dataSet[0].contact__prenom );
    }else {  
    $('#AjaxContactCMD').html('...') }
    if (dataSet[0].client__livraison_societe) {
    $('#AjaxLivraisonCMD').html(dataSet[0].client__livraison__adr1 + "<br>" + dataSet[0].client__livraison_ville + " " + dataSet[0].client__livraison_cp);
    }else{ $('#AjaxLivraisonCMD').html(dataSet[0].client__adr1 + "<br>" + dataSet[0].client__ville + " " + dataSet[0].client__cp ) }
    $('#AjaxEtatCMD').text(dataSet[0].keyword__lib);
    $('#AjaxPortCMD').html(dataSet[0].cmd__port + ' €' ) ;
    let listOfItem = $('#listOfAjaxCMD');
    listOfItem.html(' ');
    let array = dataSet[1];
    for (let index = 0; index < array.length ; index++) {
    let li = document.createElement('li');
    let content = document.createTextNode( array[index].cmdligne__quantite + " x " +  array[index].cmdligne__designation + ' : ' + array[index].cmdligne_puht + " €" );
    li.appendChild(content);
    listOfItem.append(li);
    listOfItem.children('li').addClass('list-group-item text-white bg-secondary font-weight-bold');       
    }},
            
    error: function (err) {
    alert('error: ' + err);
}})}});



// function attribut disabled des buttons: 
let checkButtonSaisie = function(){
    let RowSaisie =  $('#saisieTable').find('tr');
    if (RowSaisie.hasClass('selected')) {
    $('#saisieButton').removeAttr('disabled');
    } else { $('#saisieButton').prop("disabled", true);}
}
checkButtonSaisie();



// function de selection de la table saisie : 
saisieTable.on('click', 'tr', function(){
    if ( $(this).hasClass('selected') ){
    $(this).removeClass('selected');}
    else if(saisieTable.rows().count() >= 1){
    $('#saisieTable tr.selected').removeClass('selected');
    $(this).addClass('selected');
    dataRow = saisieTable.row('.selected').data()[0];
    $('#saisieLivraison').val(dataRow);
     // requete Ajax sur la commande selectionnée : 
     $.ajax({
        type: 'post',
        url: "AjaxSaisie",
        data : 
        {"AjaxSaisie" : dataRow},
        success: function(data){
        dataSet = JSON.parse(data);
        $('#AjaxIdCMD').html( 'Commande N°:'+ ' ' + dataSet[0].cmd__id)
        $('#AjaxSaisie').html(dataSet[0].client__societe + "<br>" + dataSet[0].client__ville + " " + dataSet[0].client__cp );
        if (dataSet[0].contact__nom){
        $('#AjaxContactSaisie').html(dataSet[0].contact__nom + " " + dataSet[0].contact__prenom );
        }else {  
        $('#AjaxContactSaisie').html('...') }
        if (dataSet[0].client__livraison_societe) {
        $('#AjaxLivraisonSaisie').html(dataSet[0].client__livraison__adr1 + "<br>" + dataSet[0].client__livraison_ville + " " + dataSet[0].client__livraison_cp);
        }else{ $('#AjaxLivraisonSaisie').html(dataSet[0].client__adr1 + "<br>" + dataSet[0].client__ville + " " + dataSet[0].client__cp ) }
        $('#AjaxEtatSaisie').text(dataSet[0].keyword__lib);
        $('#AjaxPortSaisie').html(dataSet[0].cmd__port + ' €' ) ;
        let listOfItem = $('#listOfAjaxSaisie');
        listOfItem.html(' ');
        let array = dataSet[1];
        for (let index = 0; index < array.length ; index++) {
        let li = document.createElement('li');
        let content = document.createTextNode( array[index].cmdligne__quantite + " x " +  array[index].cmdligne__designation + ' : ' + array[index].cmdligne_puht + " €" );
        li.appendChild(content);
        listOfItem.append(li);
        listOfItem.children('li').addClass('list-group-item text-white bg-secondary font-weight-bold');       
        }},
                
        error: function (err) {
        alert('error: ' + err);
    }})}
    
    checkButtonSaisie();
})




//init de la table utilisateur: 

let userTable = $('#userTable').DataTable({
    "paging": true,
    "info": true,
    retrieve: true,
    "deferRender": true,
    "searching": true,
    "columnDefs": [{
        "targets": [ 0 ],
        "visible": false,
        "searchable": false
    },]
})





userTable.on('click', 'tr', function(){
    
    if ( $(this).hasClass('selected') ){
    $(this).removeClass('selected');
    $('#buttonUser').prop("disabled", true);}

    else if(userTable.rows().count() >= 1){
   
    $('#userTable tr.selected').removeClass('selected');
    $(this).addClass('selected');
    $('#buttonUser').removeAttr('disabled');
    dataRow = userTable.row('.selected').data()[0];
    console.log(dataRow);
    $("#modifyUser").val(dataRow);
}})




//reload de la page sur post fiche de travail : 
$('#TravailButton').on('click', function(){
    let reload = window.location.reload;
    setTimeout(window.location.reload.bind(window.location),2500);
})


 












































})