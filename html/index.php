<?php
session_start();
/*
 * Load reference to include directory
 */
include_once '../config/config.php';
include __DIR__ . '/../inc/header.php';
?>

<body>
    <?php include __DIR__ . '/../inc/navbar.php'; ?>
    
<div class="container">

<?php

if(!isset($_SESSION['fb_access_token'])) {
    include '../views/welcome.html';
} else if(isset($_GET['type'])){
    //Parse the type, ie text, image, both
    switch ($_GET['type']) {
        case 'text':
            include '../views/post_status.html';
            break;
        case 'photo':
            include '../views/post_photo.html';
            break;
        case 'overlay':
            include '../views/post_overlay.html';
            break;
        case 'soap':
            include '../views/soap_details.html';
            break;
        default:
            include '../views/welcome_authed.html';
    }
} else {
    include '../views/welcome_authed.html';
}

?>

</div><!--/.container-->
<?php
include __DIR__ . '/../inc/scripts.php';
?>
</body>
</html>