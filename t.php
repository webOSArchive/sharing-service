<?php
// T (Text) Endpoint
//      This endpoint supports returning the content of a text or JSON share
include("common.php");

$sharehandle = $_SERVER['QUERY_STRING'];
if (!isset($sharehandle) || $sharehandle == "")
    graceful_death("content request not specified!");

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
            if ($value['contenttype'] == "application/json") {
                header('Content-Type: application/json');
                print_r(json_encode($value['content'], JSON_PRETTY_PRINT));
            }
            else {
                header('Content-Type: text/plain');
                print_r($value['content']);
            }
        }
    }

} else {
    gracefuldeath_html("content request malformed!");
}


?>
