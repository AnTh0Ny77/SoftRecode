$(document).ready(function() 
{

  

$('#exportButton').on('click' , function(event)
{
    let min = parseInt($('#minFact').val())
    let from = parseInt($('#exportStart').val())
    let to = parseInt($('#exportEnd').val())

    if (from >= to || from < min || to < min ) 
    {
        event.preventDefault();
        $('#alertExport').removeClass('d-none');
    }
})



})