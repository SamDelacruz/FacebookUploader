<?php
use Uploader\FBUser;
session_start();
/*
 * Load reference to include directory
 */
include_once 'config.php';
include $include_dir . 'header.php';
?>

<body>
    <?php include $include_dir . 'navbar.php'; ?>
    
<div class="container">

<?php

if(!isset($_SESSION['fb_access_token'])) {
    include 'views/welcome.html';
} else if(isset($_GET['type'])){
    //Parse the type, ie text, image, both
    switch ($_GET['type']) {
        case 'text':
            include 'views/post_status.html';
            break;
        case 'photo':
            include 'views/post_photo.html';
            break;
        case 'overlay':
            include 'views/post_overlay.html';
            break;
        case 'soap':
            include 'views/soap_details.html';
            break;
        default:
            include 'views/welcome_authed.html';
    }
} else {
    include 'views/welcome_authed.html';
}

?>

</div><!--/.container-->
<?php
include $include_dir . 'scripts.php';
?>
</body>
</html>