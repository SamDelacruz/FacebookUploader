<?php
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

if(!$_SESSION['fb_loggedIn']) {
    echo '<div class="jumbotron">';
    echo '<h1>';
    echo 'Welcome to Facebook Uploader ';
    echo '<small>Please login to continue</small>';
    echo '</h1>';
    echo '<a class="btn btn-primary" role="button" href="login.php">Login with Facebook</a>';
    echo '</div>';
    
} else {
    echo '<div class="col-sm-8 col-sm-offset-2 col-md-offset-3 col-md-6">';
        echo '<form method="post" id="fb_status_form">';
            echo '<textarea id="fb_status" name="fb_status" class="form-control input-large" rows="3" placeholder="Facebook Status"></textarea>';
            echo '<button class="btn btn-primary btn-block" type="button" onclick="post_update()">Post</button>';
        echo '</form>';
        echo '<div class="alert alert-success result hidden">';
        echo '</div>';
    echo '</div>';
}

?>

</div><!--/.container-->
<?php
include $include_dir . 'scripts.php';
?>
</body>
</html>