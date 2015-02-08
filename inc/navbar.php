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
                    if(!$_SESSION['fb_loggedIn']) {
                        echo '<li><a href="login.php">Login with Facebook</a></li>';
                    } else {
                        echo '<li class="dropdown">';
                            echo '<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">'
                                . '<img class="img-circle" width="18px" height="18px" src="'. $_SESSION['fb_picture_url'] .'" />&nbsp;'
                                . $_SESSION['fb_first_name'] . ' ' . $_SESSION['fb_last_name'] . '<span class="caret"></span></a>';
                            echo '<ul class="dropdown-menu" role="menu">';
                            echo '<li><a href="' . $_SESSION['fb_logoutURL'] . '">Logout</a></li>';
                            echo '</ul>';
                        echo '</li>';
                        
                    }
                ?>
                
            </ul>
        </div><!--/.nav-collapse -->
    </div>
</nav>