


$(document).ready(function() {

//init du commentaire Interne global : 
if ($('#globalComInt').length) 
{
    ClassicEditor       
    .create( document.querySelector( '#globalComInt' ) ,{
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
        [
            'heading', '|',  'bold', 'italic', 'bulletedList', 'numberedList' , 'link', '|', 'undo' , 'redo' , 'fontColor'
        ]
         })
         .catch( error =>
         {
             console.error( error );
         });     
}

//init du commentaire Client global : 
if ($('#globalComClient').length) 
{
    ClassicEditor       
    .create( document.querySelector( '#globalComClient' ) ,{
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
        [
            'heading', '|',  'bold', 'italic', 'bulletedList', 'numberedList' , 'link', '|', 'undo' , 'redo' , 'fontColor'
        ]
         })
         .catch( error =>
         {
             console.error( error );
         });     
}



//fonction de remplissage des select contact en fonction de leur client : 

$('#clientSelect').on('click' , function()
{
    console.log('hey');
})



})


