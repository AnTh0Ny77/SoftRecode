$(document).ready(function() 
{

   
     $('#client_input').on('keyup' , function()
     {
        let recherche = $('#client_input').val();

        if (recherche.length > 2 ) 
        {
           
            $.ajax(
                {
                    type: 'post',
                    url: "Ajax_search_client",
                    data : 
                    {
                        "search" : recherche
                    }, 
        
                    beforeSend: function() 
                    {
                    
                    },
        
                    complete: function()
                    {
                    
                    }, 
        
                    success: function(data)
                    {
                        console.log(data);
                        dataSet = JSON.parse(data);   
                    },
                            
                    error: function (err) 
                    {
                        console.log('error: ' + err);
                    }
                })
        }
     })
})
    
    
    