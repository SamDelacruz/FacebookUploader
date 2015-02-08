var post_update = function() {
    var fb_status = document.getElementById('fb_status').value;
    document.getElementById('fb_status').value = '';
    console.log(fb_status);
    $.ajax({
        'type': "POST",
        'url': "http://localhost:8888/status.php",
        'data': {'fb_status': fb_status},
        'success': post_callback,
        'fail': post_callback_fail
    });
    
};

var post_callback = function(data) {
    var output = "";
    if(data.indexOf("http://") > -1) {
        output = '<a target="_blank" href="' + data + '">View on Facebook</a>';
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