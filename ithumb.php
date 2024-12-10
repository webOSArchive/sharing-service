<?php
// ithumb Endpoint
//      This endpoint creates (if needed) and returns a smaller version of an image share as binary data that can be the source of an HTML img element
include ("common.php");
require __DIR__ . '/vendor/autoload.php';

//Handle more specific queries
$itemid = null;
$imgSize = 128;
if (isset($_GET["size"]))
    $imgSize = $_GET["size"];
if (isset($_GET['itemid']) && $_GET['itemid'] != "") {
    $itemid = $_GET['itemid'];
} else { //Accept a blanket query
    if (isset($_SERVER['QUERY_STRING']) && $_SERVER['QUERY_STRING'] != "")
        $itemid = $_SERVER['QUERY_STRING'];
}
if (!isset($itemid)) {    //Deal with no usable request
    gracefuldeath_httpcode(400);
}

$sharehandle = base64url_decode($itemid);
$shareparts = explode("|", $sharehandle);
if (count($shareparts) > 1) {
    $username = strtolower($shareparts[0]);
    $contentid = $shareparts[1];

    if (!is_dir("data/" . $username)) {
        gracefuldeath_httpcode(417);
    }

    //Make sure the file exists and can be loaded
    $jsondata = get_share_data($username, $config['readonlykey'], 'gracefuldeath_httpcode');
    $found = false;
    foreach ($jsondata['shares'] as $share => $value) {
        if ($contentid == $value['guid'])
        {
            $found = true;
            //Prepare the cache name
            $cacheID = "thumb-" . $value['guid'];
            $path = "data/" . $username;

            //Fetch and cache the file if its not already cached
            $path = $path . "/" . $cacheID . ".png";
            if (!file_exists($path)) {
                resize_img($imgSize, $path, $value['content']);
            }

            //Send the right headers
            $info = getimagesize($path);
            header("Content-Type: " . $info['mime']);
            header("Content-Length: " . filesize($path));
            //Dump the file and stop the script
            $fp = fopen($path, 'r');
            fpassthru($fp);
            exit;
        }
    }
    if (!$found) {
        gracefuldeath_httpcode(410);
    }

} else {
    gracefuldeath_httpcode(400);
}

//Function to resize common image formats
//  Found on https://stackoverflow.com/questions/13596794/resize-images-with-php-support-png-jpg
function resize_img($newWidth, $targetFile, $originalFile) {

    $info = getimagesize($originalFile);
    if (is_array($info))
        $mime = $info['mime'];
    else
        $mime = "unknown";

    switch ($mime) {
            case 'image/jpeg':
                $image_create_func = 'imagecreatefromjpeg';
                $image_save_func = 'imagejpeg';
                $new_image_ext = 'jpg';
                break;

            case 'image/png':
                $image_create_func = 'imagecreatefrompng';
                $image_save_func = 'imagepng';
                $new_image_ext = 'png';
                break;

            case 'image/gif':
                $image_create_func = 'imagecreatefromgif';
                $image_save_func = 'imagegif';
                $new_image_ext = 'gif';
                break;

            default: 
                $fileIsHeic = Maestroerror\HeicToJpg::isHeic($originalFile);
                if ($fileIsHeic) {
                    error_log("HEIC detected, attempting conversion!");
                    Maestroerror\HeicToJpg::convert($originalFile, __DIR__ . "/vendor/bin/heif-converter-linux")->saveAs($originalFile);
                    $image_create_func = 'imagecreatefromjpeg';
                    $image_save_func = 'imagejpeg';
                    $new_image_ext = 'jpg';
                } else {
                    throw new Exception('Unknown image type.');
                }
                break;
    }

    $img = $image_create_func($originalFile);
    list($width, $height) = getimagesize($originalFile);

    $newHeight = ($height / $width) * $newWidth;
    $tmp = imagecreatetruecolor($newWidth, $newHeight);
    imagesavealpha($tmp, true);
    $trans_colour = imagecolorallocatealpha($tmp, 0, 0, 0, 127);
    imagefill($tmp, 0, 0, $trans_colour);

    imagecopyresampled($tmp, $img, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

    if (file_exists($targetFile)) {
            unlink($targetFile);
    }
    $image_save_func($tmp, $targetFile);
}
?>
