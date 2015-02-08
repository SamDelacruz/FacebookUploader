<?php
/*
 * All footer scripts here
 */
?>
<script src="//ajax.googleapis.com/ajax/libs/jquery/2.0.0/jquery.min.js"></script>
<script src="vendor/kartik-v/bootstrap-fileinput/js/fileinput.min.js"></script>
<script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.2/js/bootstrap.min.js"></script>
<script src="js/post_update.js"></script>
<script>
    $("#fb_image").fileinput(({
        'uploadAsync' : false,
        'allowedFileExtensions' : ['jpg', 'jpeg', 'png','gif'],
        'maxFilesNum' : 1,
        'uploadUrl' : 'http://localhost:8888/photo.php',
        'dropZoneEnabled' : false,
        'layoutTemplates' : {
           actionUpload: ''
        },
        'uploadExtraData' : function () {
            return {'fb_status' : $("#fb_status").val()};
        }
    }));

    $('#fb_image').on('fileuploaded', function(event, data, previewId, index) {
        var form = data.form, files = data.files, extra = data.extra,
            response = data.response, reader = data.reader;
        console.log(response);
    });
    
</script>