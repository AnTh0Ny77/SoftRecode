
$(document).ready(function (){

    let user = $('#ticketUser').val();
    console.log(user);
    $.ajax({
        type: 'post',
        url: "ajaxTicket",
        data:{"id": user},
        success: function (data){
            dataSet = JSON.parse(data);
            if (dataSet['1'].length >  0) {
                $('#ticketNonLu').text(dataSet['1'].length);
                $('#notifTickets').removeClass('d-none');
            }
            if (dataSet['2'].length >  0) {
                $('#ticketEncours').text(dataSet['2'].length);
                $('#notifTicketsCours').removeClass('d-none');
            }
        },
        error: function (err) {
            console.log('error: ', err);
        }
    })

    $.ajax({
        type: 'post',
        url: "ajaxMyrecode",
        data:{"id": user},
        success: function (data){
            console.log(data);
            dataSet = JSON.parse(data);
            
            
            if (dataSet['2']  >  0) {
                $('#NBTKM').text(dataSet['2']);
                $('#notifTKM').removeClass('d-none');
            }
            if (dataSet['1']  >  0) {
                $('#NBTKMC').text(dataSet['1']);
                $('#notifcoursTKM').removeClass('d-none');
            }
        },
        error: function (err) {
            console.log('error: ', err);
        }
    })
})

