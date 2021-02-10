$(document).ready(function()
{

  //initialization des tooltips 
  $(function () {
    $('[data-toggle="tooltip"]').tooltip({ html: true })
  })
  
  $('#click_chiffre').on('click' , function()
  {
    $('#check_commande').prop( "checked", false );
    $('#form_stat').submit();
  })

  $('#click_commande').on('click' , function()
  {
    $('#check_commande').prop( "checked", true );
    $('#form_stat').submit();
  })



   google.charts.load('current', {'packages':['corechart']});
   google.charts.setOnLoadCallback(drawVisualization);
   function drawVisualization() 
  {
        data = JSON.parse($('#chartsResponse').val());
        var data = google.visualization.arrayToDataTable(data);
        var options = 
          {
            title: 'Chiffre par prestation'
          };
        var chart = new google.visualization.PieChart(document.getElementById('chartsDiv1'));
        chart.draw(data, options);
  }
    //camenbert : 
    google.charts.setOnLoadCallback(drawChart);
    function drawChart() 
    {
        $dataVendeur = JSON.parse($('#chartsVendeur').val())
        var data = google.visualization.arrayToDataTable( $dataVendeur);
        var options = 
        {
          title: 'Chiffre par vendeur'
        };
        var chart = new google.visualization.PieChart(document.getElementById('CamVendeur'));
        chart.draw(data, options);
      }
})