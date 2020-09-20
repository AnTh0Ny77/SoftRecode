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
})    