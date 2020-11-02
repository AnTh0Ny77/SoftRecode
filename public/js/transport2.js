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

      
        if(isNaN(poidsNb) || parseInt(poidsNb) == 0 ) 
        { 
           
           alert('Le poids est incorrect')
        }
        else
        { 
            $('#valid-saisie').submit();
        
        }
    })
})