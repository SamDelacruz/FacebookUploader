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
    console.log('sent');
    $(".result").html(data);
    $(".result").removeClass("hidden");
};

var post_callback_fail = function(data) {
    console.log('fail');
    $(".result").html(data);
    $(".result").removeClass("hidden");;
};