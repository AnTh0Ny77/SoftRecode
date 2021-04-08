$(document).ready(function() {
           // commentaire interne resaisie par le client: 
           ClassicEditor
           .create(document.querySelector('#commentaireInterneValid'),
               {
                   fontColor: {
                       colors: [
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
                   toolbar: [ 'heading', '|',  'bold', 'italic', 'bulletedList', 'numberedList' , 'link', '|', 'undo' , 'redo' ,  'fontColor']
               })
               .then( newEditor => 
               {
               ckUpInt = newEditor;
               })
               .catch( error => 
               {
               console.error( error );
               });

            arrayCommentaires = [];

            
             arrayLine  = $('#arrayOfLines').val();
   
             arrayLine = JSON.parse(arrayLine);
    console.log(arrayLine);
             for (let index = 1; index <= arrayLine.length; index++) {
                ClassicEditor
                .create(document.querySelector('#cmdInterneNote'+index+''),
                    {
                        fontColor: {
                            colors: [
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
                        toolbar: [ 'heading', '|',  'bold', 'italic', 'bulletedList', 'numberedList' , 'link', '|', 'undo' , 'redo' ,  'fontColor']
                })
                .then( newEditor => 
                {

                    ckUpInt[index] = newEditor;
                    ckUpInt[index].name = arrayLine[index-1].devl__ordre;
                    arrayCommentaires.push(ckUpInt[index]);
                   
                   
                })
                .catch( error => 
                {
                    console.error( error );
                }); 
    }
               
    //recupere les inputs quantités:
    nodeQuantité = document.querySelectorAll('.QTE-CMD');
    

    // fonction de validation de commandes : 
     $('#SendCmd').on('click', function(){

        radio = $(".radioCmd");
        radioP = $('.radioCmdP');
        arrayOFsheet = [];
        arrayOfItem = JSON.parse($('#arrayOfLines').val());

        
        
        //input radio
        for (let nb = 0; nb < radio.length; nb++) 
        {
            if(radio[nb].checked == true)
            {
                
                arrayTemp = [];
                arrayTemp.push(radio[nb].value,radioP[nb].value,radio[nb].id );
                arrayOFsheet.push(arrayTemp);    
            }
        }
         
        
        for (let index = 0; index < arrayOfItem.length; index++) 
        {
          
            if (arrayOFsheet[index] === undefined) 
            {
                arrayOFsheet[index] = [null,null];
            }
          
            for(let y = 0; y < arrayOFsheet.length; y++) 
            {    
                if (arrayOfItem[index].devl__ordre == arrayOFsheet[y][2]) 
                {   
                    arrayOFsheet[y].pop();
                    arrayOfItem[index].devl__prix_barre = arrayOFsheet[y];
                }       
               
            }
            
           //commentaire interne pour chaque ligne:
           if (parseInt(arrayCommentaires[index].name) == parseInt(arrayOfItem[index].devl__ordre) ) 
           {
                arrayOfItem[index].devl__note_interne = arrayCommentaires[index].getData();
           }

           //quantite pour chaque ligne:
           if (parseInt(nodeQuantité[index].id) == parseInt(arrayOfItem[index].devl__id)) 
           {
                arrayOfItem[index].devl_quantite = parseInt(nodeQuantité[index].value);
           }           
      }
      $('#arrayLigneDeCommande').val(JSON.stringify(arrayOfItem));
      let dataCom = ckUpInt.getData();
      
      $('#ComInterCommande').val(dataCom);
     
      $('#code_cmd').val($('#codeClient').val());
      
         console.log(arrayOfItem);
     $('#formValideCMD').submit();
    })


    $('#FTCADMIN').on('click', function()
    {
        $('#alertModal').modal('show');
    })

    $('#postADMINButtonAvoir').on('click' , function()
    {
        $('#formPRINTAVOIR').submit();

    })
    
    $('#AVRADMIN').on('click', function()
    {
        $('#alertModalAvoir').modal('show');
    }) 
    
    $('#postADMINButton').on('click' , function()
    {
        $('#formPRINTADMIN').submit();

    })

})