<?php
// Download Endpoint
//      This endpoint returns a shared image as a file that can be downloaded
include("common.php");

$sharehandle = $_SERVER['QUERY_STRING'];
if (!isset($sharehandle) || $sharehandle == "")
    gracefuldeath_html("content request not specified!");

//handle Facebook
if (strpos($sharehandle, "&fbclid")) {
    $sharehandle = str_replace("%3D", "=", $sharehandle);
    $stripFB = explode("&fbclid", $sharehandle);
    $sharehandle = $stripFB[0];
}

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
        //print_r($value);
        if ($contentid == $value['guid'])
        {
            if (strpos($_SERVER['HTTP_USER_AGENT'], "hpwOS") || strpos($_SERVER['HTTP_USER_AGENT'], "webOS")) {
                echo '<img src="i.php?'. $_SERVER['QUERY_STRING'] . '">';
            } else {
                header('Content-Description: File Transfer');
                header('Content-Type: application/octet-stream');
                header('Content-Disposition: attachment; filename="'.basename($value['content']).'"');
                header('Expires: 0');
                header('Cache-Control: must-revalidate');
                header('Pragma: public');
                header('Content-Length: ' . filesize($value['content']));
                flush(); // Flush system output buffer
                readfile($value['content']);
            }
        }
    }

} else {
    gracefuldeath_html("content request malformed!");
}
?>
