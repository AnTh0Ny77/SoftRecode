$(document).ready(function()
{
  

   google.charts.load('current', {'packages':['corechart']});
   google.charts.setOnLoadCallback(drawVisualization);
   function drawVisualization() 
    {
        data = JSON.parse($('#chartsResponse').val());
       
        var data = google.visualization.arrayToDataTable(data);

        var options = {
            title: 'Chiffre par prestation pour les filtres selectionnés'
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

        var options = {
          title: 'Chiffre par vendeur dates selectionnées'
        };

        var chart = new google.visualization.PieChart(document.getElementById('CamVendeur'));
        chart.draw(data, options);
      }



  
   
  

})