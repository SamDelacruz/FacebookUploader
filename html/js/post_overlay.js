document.querySelector('#preview-button').addEventListener('click', function(e) {
    sendRequest('preview');
    e.preventDefault();
    return false;
}, false);

document.querySelector('#publish-button').addEventListener('click', function(e) {
    sendRequest('publish');
    e.preventDefault();
    return false;

}, false);

var sendRequest = function(mode) {
    var xhr = new XMLHttpRequest();
    var file = document.getElementById('fb_image_overlay');
    var fbStatus = document.getElementById('fb_status').value;
    var overlay = document.getElementById('fb_overlay_text').value;
    $('#preview-button').addClass('disabled');
    $('#publish-button').addClass('disabled');
    var formData = new FormData();
    formData.append("fb_image", file.files[0]);
    formData.append("fb_status", fbStatus);
    formData.append("fb_overlay_text", overlay);
    formData.append("mode", mode);
    xhr.open("POST", "/overlay.php", true);

    xhr.onload = function() {
        if(this.status == 200) {
            var resp = JSON.parse(this.response);
            var image = document.createElement('img');
            $('#img-target').attr('src', resp.imageUrl);
            $('#img-target').removeClass('hidden');
            $('#preview-button').removeClass('disabled');
            $('#publish-button').removeClass('disabled');
            var output = "";
            if(resp.url) {
                output = '<strong>Post Successful: </strong><a target="_blank" href="' + resp.url + '">View on Facebook</a>';
            } else {
                output = "<pre>" + data + "</pre>";
            }
            $(".result").html(output);
            $(".result").removeClass("hidden");
        };
    };

    xhr.send(formData);
    
}

$("#fb_image_overlay").fileinput(({
    'showPreview': false,
    'showUpload': false,
    'showCancel': false,
    'showRemove': false,
    'allowedFileExtensions' : ['jpg', 'jpeg', 'png','gif'],
    'maxFilesNum' : 1,
    'uploadUrl' : 'http://localhost:8888/overlay.php',
    'dropZoneEnabled' : false,
    'layoutTemplates' : {
        actionUpload: ''
    },
    'uploadExtraData' : function () {
        return {'fb_status' : $("#fb_status").val()};
    }
}));

$('#fb_image_overlay').change(function() {
    if(this.files[0] != null) {
        $('#preview-button').removeClass('disabled');
        $('#publish-button').removeClass('disabled');
        readURL(this)
    } else {
        if(!$('#preview-button').hasClass('disabled')) {
            $('#preview-button').addClass('disabled');
            $('#publish-button').addClass('disabled');
        }
    }
});

var readURL = function(input) {

    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function (e) {
            $('#img-target').attr('src', e.target.result).removeClass('hidden');
        }

        reader.readAsDataURL(input.files[0]);
    }
}
