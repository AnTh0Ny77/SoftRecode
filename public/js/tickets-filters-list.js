$(document).ready(function(){
    $('#filtersTickets').multiselect({
        buttonWidth: 300,
        selectAllNumber: true,
        nonSelectedText : 'Aucun filtres',
        allSelectedText: 'Tous les filtres sont sélectionnés'
    });
    $('#filtersEtat').multiselect({
        buttonWidth: 200,
        selectAllNumber: true,
        nonSelectedText : 'Aucun filtres', 
        allSelectedText: 'Tous les filtres sont sélectionnés'
    });
    $('#filtersType').multiselect({
        buttonWidth: 200,
        selectAllNumber: true,
        nonSelectedText : 'Aucun filtres', 
        allSelectedText: 'Tous les filtres sont sélectionnés'
    });

    
})