<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once __DIR__ . '/vendor/autoload.php';
session_start();
use Uploader\FBPhotoHandler;
use Facebook\FacebookRequestException;

function imagecreatefromfile( $filename ) {
    if (!file_exists($filename)) {
        throw new InvalidArgumentException('File "'.$filename.'" not found.');
    }
    switch ( strtolower( pathinfo( $filename, PATHINFO_EXTENSION ))) {
        case 'jpeg':
        case 'jpg':
            return imagecreatefromjpeg($filename);
            break;

        case 'png':
            return imagecreatefrompng($filename);
            break;

        case 'gif':
            return imagecreatefromgif($filename);
            break;

        default:
            throw new InvalidArgumentException('File "'.$filename.'" is not valid jpg, png or gif image.');
            break;
    }
}

function saveImage($image, $filename) {
    if (!file_exists($filename)) {
        throw new InvalidArgumentException('File "' . $filename . '" not found.');
    }
    switch (strtolower(pathinfo($filename, PATHINFO_EXTENSION))) {
        case 'jpeg':
        case 'jpg':
            unlink($filename);
            return imagejpeg($image, $filename);
            break;

        case 'png':
            unlink($filename);
            return imagepng($image, $filename);
            break;

        case 'gif':
            unlink($filename);
            return imagegif($image, $filename);
            break;

        default:
            throw new InvalidArgumentException('File "' . $filename . '" is not valid jpg, png or gif image.');

    }
}

$fileName = $_FILES['fb_image']['name'];
$fileType = $_FILES['fb_image']['type'];
$fileContent = file_get_contents($_FILES['fb_image']['tmp_name']);//TODO: Get rid of this
$imageUrl = 'data:' . $fileType . ';base64,' . base64_encode($fileContent);//TODO: replace with fixed url
$mode = $_POST['mode'];
$fb_status = $_POST['fb_status'];
$fb_overlay_text = $_POST['fb_overlay_text'];

$images = $_FILES['fb_image'];

$ext = explode('.', basename($images['name']));
$target = "uploads" . DIRECTORY_SEPARATOR . md5(uniqid()) . "." . array_pop($ext);
$success = null;

if(move_uploaded_file($images['tmp_name'], $target)) {
    $success = true;
} else{
    $success = false;
}

$response_stack = [];
$response_stack['name'] = $fileName;
$response_stack['type'] = $fileType;
$response_stack['imageUrl'] = $target;
$response_stack['mode'] = $mode;

if ($success === true) {
    $image = imagecreatefromfile($target);
    $white = imagecolorallocate($image, 255,255,255);
    $black = imagecolorallocate($image, 0,0,0);
    $font_path = "../fonts/Cabin-Medium.ttf";

    $img_width = imagesx($image);
    $img_height = imagesy($image);
    
    $font_size = 1;
    $txt_max_width = intval(0.8 * $img_width);

    do {
        $font_size++;
        $p = imagettfbbox($font_size,0,$font_path,$fb_overlay_text);
        $txt_width=$p[2]-$p[0];
    } while ($txt_width <= $txt_max_width);

    $y = $img_height * 0.9;
    $x = ($img_width - $txt_width) / 2;
    $stroke_width = floor($font_size * 0.03125);

    // Draw the border
    ImageTTFText($image, $font_size, 0, $x + $stroke_width, $y, $black, $font_path, $fb_overlay_text);
    ImageTTFText($image, $font_size, 0, $x - $stroke_width, $y, $black, $font_path, $fb_overlay_text);
    ImageTTFText($image, $font_size, 0, $x, $y + $stroke_width, $black, $font_path, $fb_overlay_text);
    ImageTTFText($image, $font_size, 0, $x, $y - $stroke_width, $black, $font_path, $fb_overlay_text);
    
    // Draw the message
    ImageTTFText($image, $font_size, 0, $x, $y, $white, $font_path, $fb_overlay_text);
    
    saveImage($image, $target);
    imagedestroy($image);

    if($mode === 'publish') {
        try{
            $response = (new FBPhotoHandler(
                $_SESSION['fb_access_token']
            ))->postPhoto($fb_status, $target);
        } catch (FacebookRequestException $ex) {
            echo json_encode($ex->asArray());
        } catch (RuntimeException $ex) {
            echo json_encode($ex->asArray());
        }
        $hasPosted = !strpos($response, '_') === false;
        if($hasPosted) {
            $split = explode('_', $response);
            $postUrl = "http://facebook.com/" . $split[0] . "/posts/" . $split[1];
            $response_stack['url'] = $postUrl;
        } else {
            $response_stack['error'] = $response;
        }
    }
    
}

$json = json_encode($response_stack);

echo $json;

