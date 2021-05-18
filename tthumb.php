<?php
include ("common.php");

//TODO: Try this: https://code-boxx.com/convert-text-image-php/


//Handle more specific queries
$img = null;
$imgSize = 128;
if (isset($_GET["size"]))
    $imgSize = $_GET["size"];
if (isset($_GET['img']) && $_GET['img'] != "") {
    $img = $_GET['img'];
} else { //Accept a blanket query
    if (isset($_SERVER['QUERY_STRING']) && $_SERVER['QUERY_STRING'] != "")
        $img = $_SERVER['QUERY_STRING'];
}
if (!isset($img)) {    //Deal with no usable request
    header($_SERVER["SERVER_PROTOCOL"] . " 400 Bad Request");
    die;
}


$sharehandle = $_SERVER['QUERY_STRING'];
if (!isset($sharehandle) || $sharehandle == "")
    graceful_death("content request not specified!");

$sharehandle = base64url_decode($sharehandle);
$shareparts = explode("|", $sharehandle);

if (count($shareparts) > 1) {
    $username = $shareparts[0];
    $contentid = $shareparts[1];

    if (!is_dir("data/" . $username)) {
        gracefuldeath_html("user does not exist!");
    }

    //Make sure the file exists and can be loaded
    $jsondata = get_share_data($username, $config['readonlykey'], 'gracefuldeath_html');
    foreach ($jsondata['shares'] as $share => $value) {
        if ($contentid == $value['guid'])
        {
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

} else {
    gracefuldeath_html("content request malformed!");
}

//Function to resize common image formats
//  Found on https://stackoverflow.com/questions/13596794/resize-images-with-php-support-png-jpg
function resize_img($newWidth, $targetFile, $originalFile) {

    $info = getimagesize($originalFile);
    $mime = $info['mime'];

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
                    throw new Exception('Unknown image type.');
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
