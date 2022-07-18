$(document).ready(function () {



    //open modal
    $('#recherchePnButton').on('click' , function(){
        $('#modalPnRecherche').modal('show')
    })

    //close modal 
    $('#CloseModalRecherche').on('click', function () {
        $('#modalPnRecherche').modal('hide')
    })


    $('#SearchPnModal').on('click' , function(){
        var selectedPn = $('#select_pn_search').children("option:selected").val();
        console.log(selectedPn);
        if (selectedPn != null) {
            $.ajax(
                {
                    type: 'post',
                    url: "ajax-selected-pn",
                    data: { "pn": selectedPn },
                    success: function (data){
                        
                        dataSet = JSON.parse(data);
                        $('#fmm').val(parseInt(dataSet.id__fmm));
                        $('.selectpicker').selectpicker('refresh');
                        console.log(dataSet);
                        get_pn_and_refresh(selectedPn);
                        if (dataSet.apn__design_com.length) {
                            $('#designation').val(dataSet.apn__design_com);
                        } else { $('#designation').val(dataSet.apn__desc_short); }
                        if (dataSet.apn__desc_long.length ) {
                            CKEDITOR.instances['comClient'].insertHtml(dataSet.apn__desc_long);
                        }else { CKEDITOR.instances['comClient'].setData(''); }

                        $('#modalPnRecherche').modal('hide');
                       
                    },
                    error: function (err) {
                        console.log('error: ' + err);
                    }
                })  
        }
    })
    
    let get_pn_and_refresh = function ($pn) {
        let modele = $('#fmm').children("option:selected").val();
        $.ajax({
                type: 'post',
                url: "AjaxPn",
                data:{"AjaxPn": modele},
                success: function (data) {
                    dataSet = JSON.parse(data);
                    if (dataSet.length > 0) {
                        $('#wrapper-pn').removeClass('d-none');
                        $('#pn-select').find('option').remove();
                        $('#pn-select').selectpicker('refresh');
                        $("#pn-select").append(new Option('Non spécifié', '0'))
                        for (let index = 0; index < dataSet.length; index++) {
                            if (dataSet[index].apn__desc_short == null) {
                                dataSet[index].apn__desc_short = '';
                            }
                            $("#pn-select").append(new Option(dataSet[index].apn__pn_long + " " + dataSet[index].apn__desc_short, dataSet[index].id__pn))
                        }

                        $('#pn-select').selectpicker('refresh')
                        $('#pn-select').selectpicker('val', $pn);
                    }
                    else {
                        $('#pn-select').find('option').remove();
                        $("#pn-select").append(new Option('Non spécifié', '0'))
                        $('#pn-select').selectpicker('refresh')
                        $('#pn-select').selectpicker('val', '0');
                        $('#wrapper-pn').addClass('d-none');
                    }
                },
                error: function (err) {
                    console.log('error: ', err);
                }
        })
    }

    
})