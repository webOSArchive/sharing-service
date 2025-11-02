<?php
// I (img) Endpoint
//      This endpoint supports returning the binary data of an image share that can be the source of an HTML img element
include("common.php");

if (isset($_GET["size"]))   //TODO: Accepted but not used at this time
    $imgSize = $_GET["size"];
if (isset($_GET['img']) && $_GET['img'] != "") {
    $sharehandle = $_GET['img'];
} else { //Accept a blanket query
    if (isset($_SERVER['QUERY_STRING']) && $_SERVER['QUERY_STRING'] != "")
        $sharehandle = $_SERVER['QUERY_STRING'];
}
if (!isset($sharehandle)) {    //Deal with no usable request
    gracefuldeath_httpcode(400);
}

$sharehandle = base64url_decode($sharehandle);
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
        //print_r($value);
        if ($contentid == $value['guid'])
        {
            $found = true;
            // Validate file path to prevent path traversal
            if (!validate_file_path($value['content'], $username)) {
                error_log("Path traversal attempt in i.php: " . $value['content']);
                gracefuldeath_httpcode(403);
            }
            header('Content-Type '. $value['contenttype']);
            $fp = fopen($value['content'], 'rb');
            fpassthru($fp);
        }
    }
    if (!$found) {
        $fp = fopen('images/share-missing.png', 'rb');
        fpassthru($fp);
    }

} else {
    gracefuldeath_httpcode(400);
}
?>
