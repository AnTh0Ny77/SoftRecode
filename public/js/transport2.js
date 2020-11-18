$(document).ready(function() 
{

    //num pad javascript: 
    let entry = '';
    $('.calcuClick').on('click', function(){
         entry += this.value;
         entry = entry.toString();
        $('#calc_resultat').val(entry);
    })

    $('#delete').on('click' , function(){
       entry =  entry.substring(0, entry.length - 1);
        $('#calc_resultat').val(entry);
    })


    //formulaire de validation:
    let bindData = function()
    {
         let poids = $('#calc_resultat').val();
        if (poids.length == 0 ) 
        {
            poids = 00 ;
        }
        
         let transporteur = $('#select-transport').val();
       

         let paquet = $('#select-transporteur').val();
         $('#poids').val(poids);
         $('#transporteur').val(transporteur);
         $('#paquets').val(paquet);

        
    }

    $('#post-saisie').on('click', function(){
        bindData();
       
        var poidsNb = $('#poids').val();
        var paquetNb = parseInt($('#select-transporteur').val());
        

        if(isNaN(poidsNb) || parseInt(poidsNb) == 0 ) 
        { 
           
           alert('Le poids est incorrect')
        }
        else if ($('#transporteur').val() == 'TNT' && parseInt(poidsNb)/paquetNb > 30 ) 
        {
            alert('Paquet supérieur à 30 kilos !! ');
        }
        else if ($('#transporteur').val().length < 1) 
        {
            alert('Choisi un transporteur !! ')
        }
        else
        { 
            $('#valid-saisie').submit();
        
        }
    })
})