$(document).ready(function() {


// intialisation de la table des commandes en cours : 
 let commandTableCours = $('#commandCoursTable').DataTable({
"paging": true,
"info": true,
retrieve: true,
"deferRender": true,
"searching": true,
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
$('#travailFiche').val(idRow);
checkButtonTravail();
}});

















































})