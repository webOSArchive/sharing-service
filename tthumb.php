<?php
// ithumb Endpoint
//      This endpoint creates (if needed) and returns an image version of a text share as binary data that can be the source of an HTML img element
//      It needs work.
include ("common.php");

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
    header($_SERVER["SERVER_PROTOCOL"] . " 400 Bad Request");
    die;
}

$sharehandle = base64url_decode($itemid);
$shareparts = explode("|", $sharehandle);
if (count($shareparts) > 1) {
    $username = strtolower($shareparts[0]);
    $contentid = $shareparts[1];

    if (!is_dir("data/" . $username)) {
        gracefuldeath_html("user does not exist!");
    }

    //Make sure the file exists and can be loaded
    $jsondata = get_share_data($username, $config['readonlykey'], 'gracefuldeath_html');
    foreach ($jsondata['shares'] as $share => $value) {
        if ($contentid == $value['guid'])
        {
            if ($value['contenttype'] == "application/json") {
                $usetext = json_encode($value['content']);
            }
            elseif($value['contenttype'] == "text/plain") {
                $usetext = $value['content'];
            }
            if (isset($usetext)) {
                //echo $usetext;
                //Prepare the cache name
                $cacheID = "thumb-" . $value['guid'];
                $path = "data/" . $username;
                
                //Fetch and cache the file if its not already cached
                $path = $path . "/" . $cacheID . ".png";
                if (!file_exists($path)) {
                    make_text_thumb($imgSize, $path, $usetext);
                }

                //Send the right headers
                $info = getimagesize($path);
                header("Content-Type: image/png");
                header("Content-Length: " . filesize($path));
                //Dump the file and stop the script
                $fp = fopen($path, 'r');
                fpassthru($fp);
                exit;
            }
        }
    }

} else {
    gracefuldeath_html("content request malformed!");
}

//Function to make an image square out of text
//TODO: Text does not wrap
function make_text_thumb($newWidth, $targetFile, $text) {

    $tmpimg = imagecreate($newWidth, $newWidth);
    $white = imagecolorallocate($tmpimg, 255, 255, 255);
    $black = imagecolorallocate($tmpimg, 0, 0, 0);
    //$text = wordwrap($text, 15, "\r", true);
    imagefilledrectangle($tmpimg, 0, 0, $newWidth, $newWidth, $white);
    imagestring($tmpimg, 3, 0, 0, $text, $black);
    imagepng($tmpimg, $targetFile); 
}
?>
