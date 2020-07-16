$(document).ready(function() {


//initialization de  tout les tooltips 
$(function () {
    $('[data-toggle="tooltip"]').tooltip()
})

  let ficheT = $("#ficheTable").DataTable({language:{decimal:"",emptyTable:"aucuns résultats",info:"Voir _START_ to _END_ of _TOTAL_ résultats",infoEmpty:"Voir 0 to 0 of 0 résultats",infoFiltered:"(filtré dans _MAX_ total résultats)",infoPostFix:"",thousands:",",lengthMenu:"Voir _MENU_ résultats par pages",loadingRecords:"Loading...",processing:"Processing...",search:"Recherche:",zeroRecords:"Aucun résultats",paginate:{first:"Première",last:"Dernière",next:"Suivante",previous:"Précédente"}},columnDefs:[{targets:[1],visible:!1},{targets:[0],visible:!1}],order:[[1,"desc"]],paging:!0,info:!1,pageLength:25,retrieve:!0,deferRender:!0,searching:!1});

  idUtilisateur = $('#idUtilisateur').val();

    // attribut classe selected: a la table fiche de travail sur le click:
    ficheT.on('click', 'tr', function () {
        
        $('#iframeFiche').hide();
        if ($(this).hasClass('selected')) {
            $(this).removeClass('selected');
        }
        else if (ficheT.rows().count() >= 1) {
            ficheT.$('tr.selected').removeClass('selected');
            $(this).addClass('selected');
        }
        let dataRow =ficheT.row(this).data();
        $('#iframeFiche').attr('src', '');
        $('#loaderFiche').show();
        // requete Ajax sur le devis selectionné dans la page mes devis : 
        $.ajax({
            type: 'post',
            url: "AjaxFT",
            data:
            {
                "AjaxFT": dataRow[0]
            },
            success: function (data) {
                dataSet = JSON.parse(data);
                
                $('#loaderFiche').hide();
                $('#iframeFiche').html(dataSet);
                $('#iframeFiche').show();
              
 


                ClassicEditor
                .create( document.querySelector( '#FTCOM' ) , 
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
                    ckComClient = newEditor;
                })
                .catch( error => 
                {
                console.error( error );
                });    
                $('.clickFT').on('click',  function(){
                   
                })
                
            },
            error: function (err) {
                console.log('error: ' + err);
            }
        })
    });




     // Attribue automatiquement la classe selected à la première ligne a la table fiche de travail : 
     let selectFirst = function(){
        let firstOne = $('#ficheTable').find('tr').eq(1);
        firstOne.addClass('selected');
        let dataRow = ficheT.row(0).data();
        $('#iframeFiche').attr('src', '');
        $('#iframeFiche').hide();
        $('#loaderFiche').show();
        // requete Ajax sur le devis selectionné dans la page mes devis : 
        $.ajax({
            type: 'post',
            url: "AjaxFT",
            data : 
            {
                "AjaxFT" : dataRow[0]
            },
            success: function(data){
                
                dataSet = JSON.parse(data);
                $('#loaderFiche').hide();
                $('#iframeFiche').html(dataSet);
                $('#iframeFiche').show();

                ClassicEditor
                .create( document.querySelector( '#FTCOM' ) , 
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
                    ckComClient = newEditor;
                })
                .catch( error => 
                {
                console.error( error );
                });    
        
            },
            error: function (err) {
                console.log('error: ' , err);
            }
        })
    }

    //si la longueur de la table est supérieure a zero je lance la fonction:
    if ($('#ficheTable').length > 0) {
        selectFirst();
    }


    


   


});