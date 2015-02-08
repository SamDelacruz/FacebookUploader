<?php
use Uploader\Utils\FBConfig;
session_start();
session_unset();
session_destroy();
header("Location: index.php");