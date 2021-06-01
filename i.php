<?php
// I (img) Endpoint
//      This endpoint supports returning the binary data of an image share that can be the source of an HTML img element
include("common.php");

if (isset($_GET["size"]))
    $imgSize = $_GET["size"];
if (isset($_GET['img']) && $_GET['img'] != "") {
    $sharehandle = $_GET['img'];
} else { //Accept a blanket query
    if (isset($_SERVER['QUERY_STRING']) && $_SERVER['QUERY_STRING'] != "")
        $sharehandle = $_SERVER['QUERY_STRING'];
}
if (!isset($sharehandle)) {    //Deal with no usable request
    header($_SERVER["SERVER_PROTOCOL"] . " 400 Bad Request");
    die;
}

$sharehandle = base64url_decode($sharehandle);
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
        //print_r($value);
        if ($contentid == $value['guid'])
        {
            header('Content-Type '. $value['contenttype']);
            $fp = fopen($value['content'], 'rb');
            fpassthru($fp);
        }
    }

} else {
    gracefuldeath_html("content request malformed!");
}
?>
