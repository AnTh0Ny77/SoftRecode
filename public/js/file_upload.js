
$(document).ready(function(){

    var uploader = new qq.FineUploader({
        element: document.getElementById("uploader"),
        deleteFile: {
            endpoint: 'ajax-delete-files' ,
            enabled: true,
            method: 'POST'
        },
        debug: true,
        request: {
            endpoint: 'ajax-upload-files' 
         
        } 
    }).on('error', function (event, id, name, reason) {
        console.log('error');
    }).on('complete', function (event, id, name, responseJSON) {
        console.log('ok');
    });
    console.log('hey');
})

