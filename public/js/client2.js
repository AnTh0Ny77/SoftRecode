$(document).ready(function() {


// intialisation de la table des commandes en cours : 
 let commandTableCours = $('#commandCoursTable').dataTable({
"paging": true,
"info": true,
retrieve: true,
"deferRender": true,
"searching": true,
});

//function selection dans la table commande en cours et attributs des button de post : 
commandTableCours.on('click','tr',function() {
if ( $(this).hasClass('selected') ) {
$(this).removeClass('selected');}
else {
$('#commandCoursTable tr.selected').removeClass('selected');
$(this).addClass('selected');
idRow = commandTableCours.row('.selected').data()[0];
console.log(idRow);
}});
















































})