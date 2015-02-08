<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);
require_once __DIR__ . '/../vendor/autoload.php';
use Uploader\FBUser;
?>

<nav class="navbar navbar-default navbar-static-top">
    <div class="container">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="<?php echo getenv('HOST_NAME')?>">Facebook Uploader</a>
        </div>
        <div id="navbar" class="navbar-collapse collapse">
            <ul class="nav navbar-nav">
                <li class="active"><a href="<?php echo getenv('HOST_NAME')?>">Home</a></li>
            </ul>
            <ul class="nav navbar-nav navbar-right">
                <?php
                    if(!isset($_SESSION['fb_access_token'])) {
                        echo '<li><a href="login.php">Login with Facebook</a></li>';
                    } else {
                        $user = new FBUser($_SESSION['fb_access_token']);
                        echo '<li class="dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                                    <img class="img-circle" width="18px" height="18px" src="'. $user->getPictureUrl() .'" />&nbsp;'
                                . $user->getFirstName() . ' ' . $user->getLastName() . '<span class="caret"></span></a>
                                <ul class="dropdown-menu" role="menu">
                                <li><a href="' . $_SESSION['fb_logout_url'] . '">Logout</a></li>
                                </ul>
                            </li>';
                    }
                ?>
                
            </ul>
        </div><!--/.nav-collapse -->
    </div>
</nav>