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
} else {
    include 'views/submit_post.html';
}

?>

</div><!--/.container-->
<?php
include $include_dir . 'scripts.php';
?>
</body>
</html>