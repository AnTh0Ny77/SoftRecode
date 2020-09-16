$(document).ready(function()
{

    //instancie l'éditor dans le modal: 
    ClassicEditor                   
            .create( document.querySelector('#ComInt') , 
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
                                [ 'heading', '|',  'bold', 'italic', 'bulletedList', 'numberedList' , 'link', '|', 'undo' , 'redo' , "imageUpload", 'fontColor']
                })
                .then( newEditor => 
                    {
                        ligneDit = newEditor;
                    } )
                .catch( error => 
                {
                    console.error( error );
                });    


    $('.clickTech').on('click', function()
    {
        dataFiche = this.value;
        $.ajax({
            type: 'post',
            url: "AjaxLigneFT",
            data:
            {
                "AjaxLigneFT": dataFiche
            },
            success: function (data) 
            {
                dataSet = JSON.parse(data);
                $('#titreComLigne').text(dataSet.famille__lib+ " " + dataSet.modele + " "  + dataSet.marque);

                    if (dataSet.devl__note_interne) 
                    {
                        ligneDit.setData(dataSet.devl__note_interne);
                    }

                    $('#hiddenLigne').val(dataSet.devl__id);
                    console.log($('#hiddenLigne'));
                    $('#ModalEditor').modal('show');
      
            },
            error: function (err) {
                console.log('error: ' , err);
            }

        })
      
    })
  
    

})