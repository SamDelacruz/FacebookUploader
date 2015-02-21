var post_update = function() {
    var fb_status = document.getElementById('fb_status').value;
    document.getElementById('fb_status').value = '';
    $.ajax({
        'type': "POST",
        'url': "/status.php",
        'data': {'fb_status': fb_status},
        'success': post_callback,
        'fail': post_callback_fail
    });
    
};

var post_callback = function(data) {
    var output = "";
    var parsedData = JSON.parse(data);
    if(parsedData.url) {
        output = '<strong>Post Successful: </strong><a target="_blank" href="' + parsedData.url + '">View on Facebook</a>';
    } else {
        output = "<pre>" + data + "</pre>";
    }
    $(".result").html(output);
    $(".result").removeClass("hidden");
};

var post_callback_fail = function(data) {
    var output = "<pre>" + data + "</pre>";
    $(".result").html(output);
    $(".result").removeClass("hidden");
};