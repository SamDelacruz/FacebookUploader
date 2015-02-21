<?php
require_once __DIR__ . '/../vendor/autoload.php';
session_start();
use Uploader\FBPhotoHandler;
use Facebook\FacebookRequestException;

/**
 * Returns GD Image object, using the correct imagecreate function
 * for the image file's extension.
 * @param $filename String denoting image filepath
 * @return resource Image object
 */
function imagecreatefromfile( $filename ) {
    if (!file_exists($filename)) {
        throw new InvalidArgumentException('File "'.$filename.'" not found.');
    }
    switch (strtolower(pathinfo($filename, PATHINFO_EXTENSION))) {
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

/**
 * @param $image Image object
 * @param $filename desired file name
 * @return bool Save success status
 */
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

if(!(strtoupper($_SERVER['REQUEST_METHOD']) === 'POST')) {
    http_response_code(403);
    echo '<h1>403 Forbidden</h1>';
} else {
    if (!isset($_SESSION['fb_access_token'])) {
        http_response_code(403);
    } else {
        if (empty($_FILES['file_data'])) {
            http_response_code(401);
        }

        $fileName = $_FILES['fb_image']['name'];
        $fileType = $_FILES['fb_image']['type'];
        $mode = $_POST['mode'];
        $fb_status = $_POST['fb_status'];
        $fb_overlay_text = $_POST['fb_overlay_text'];

        $images = $_FILES['fb_image'];

        $ext = explode('.', basename($images['name']));
        $target = "uploads" . DIRECTORY_SEPARATOR . md5(uniqid()) . "." . array_pop($ext);
        $success = null;

        // Try to store image in /uploads directory
        if (move_uploaded_file($images['tmp_name'], $target)) {
            $success = true;
        } else {
            $success = false;
        }

        // Build array for JSON response
        $response_stack = [];
        $response_stack['name'] = $fileName;
        $response_stack['type'] = $fileType;
        $response_stack['imageUrl'] = $target;
        $response_stack['mode'] = $mode;

        if ($success === true) {
            /*
             * Avoid infinite loop
             */
            if (strlen($fb_overlay_text) > 0) {
                $image = imagecreatefromfile($target);
                $white = imagecolorallocate($image, 255, 255, 255); // text foreground colour
                $black = imagecolorallocate($image, 0, 0, 0); // text background colour
                $font_path = "../fonts/Cabin-Medium.ttf";

                $img_width = imagesx($image);
                $img_height = imagesy($image);

                $font_size = 1;
                $txt_max_width = intval(0.8 * $img_width); // Max width of text 80% of image width

                /*
                 * Increase font size until text fits 80% image width
                 */
                do {
                    $font_size++;
                    $p = imagettfbbox($font_size, 0, $font_path, $fb_overlay_text);
                    $txt_width = $p[2] - $p[0];
                } while ($txt_width <= $txt_max_width);

                $y = $img_height * 0.9; // Text at 10% from bottom
                $x = ($img_width - $txt_width) / 2; // center text
                $stroke_width = floor($font_size * 0.03125);

                // Draw the border
                ImageTTFText($image, $font_size, 0, $x + $stroke_width, $y, $black, $font_path, $fb_overlay_text);
                ImageTTFText($image, $font_size, 0, $x - $stroke_width, $y, $black, $font_path, $fb_overlay_text);
                ImageTTFText($image, $font_size, 0, $x, $y + $stroke_width, $black, $font_path, $fb_overlay_text);
                ImageTTFText($image, $font_size, 0, $x, $y - $stroke_width, $black, $font_path, $fb_overlay_text);

                // Draw the message
                ImageTTFText($image, $font_size, 0, $x, $y, $white, $font_path, $fb_overlay_text);

                saveImage($image, $target);
                // Free up memory
                imagedestroy($image);
            }

            /*
             * Only publish to facebook if specified
             */
            if ($mode === 'publish') {
                try {
                    $response = (new FBPhotoHandler(
                        $_SESSION['fb_access_token']
                    ))->postPhoto($fb_status, $target);
                } catch (FacebookRequestException $ex) {
                    echo json_encode($ex->asArray());
                } catch (RuntimeException $ex) {
                    echo json_encode($ex->asArray());
                }
                $hasPosted = !strpos($response, 'Id') === false;
                if ($hasPosted) {
                    $split = explode(':', $response);
                    $response_stack = ['success' => $split[1]];
                } else {
                    $response_stack['error'] = $response;
                }
            }

        }

        $json = json_encode($response_stack);

        echo $json;

    }
}

