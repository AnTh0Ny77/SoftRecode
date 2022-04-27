
$(document).ready(function (){

    let user = $('#ticketUser').val();
    console.log(user);
    $.ajax({
        type: 'post',
        url: "ajaxTicket",
        data:{"id": user},
        success: function (data){
            console.log(data);
            dataSet = JSON.parse(data);
            console.log(dataSet);
        },
        error: function (err) {
            console.log('error: ', err);
        }
    })

})

