$(document).ready(function() {


    //instancie l'éditor dans le modal: 
        ClassicEditor                   
                .create( document.querySelector('#comAbn' ) , 
                                {
                                    fontColor: 
                                    {
                                        colors: 
                                        [
                                            {
                                            color: 'black',
                                            label: 'Black'
                                            },
                                            {
                                            color: 'red',
                                            label: 'Red'
                                            },
                                            {
                                            color: 'DarkGreen',
                                            label: 'Green'
                                            },
                                            {
                                            color: 'Gold',
                                            label: 'Yellow'
                                            },
                                            {
                                            color: 'Blue',
                                            label: 'Blue',
                                            },
                                        ]
                                    },
                                    toolbar: 
                                    [ 'heading', '|',  'bold', 'italic', 'bulletedList', 'numberedList' , 'link', '|', 'undo' , 'redo' , 'fontColor']
                    })
                    .then( newEditor => 
                        {
                            ligneDit = newEditor;
                        } )
                    .catch( error => 
                    {
                        console.error( error );
                    });    


    let notif_impression = $('#notif_impression').val();
   

    let send_notif = function(retour_template)
    {
        if (retour_template == 'ok') 
        {
            new jBox('Notice', 
            {
                content: 'Impression réussie ! ', 
                color: 'green' ,
               
                onInit: function() 
                    { 
                        this.open(); 
                        setTimeout(this.close() , 3);
                    }
            });
        }
        else
        {
            console.log('pas de notifications !')
        }
    }

   send_notif(notif_impression);
})    