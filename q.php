<?php
// Q (Query) Endpoint
//      This endpoint supports querying a URI for the sharedata payload
include("common.php");

$sharehandle = $_SERVER['QUERY_STRING'];
if (!isset($sharehandle) || $sharehandle == "")
    graceful_death("content request not specified!");

$sharehandle = base64url_decode($sharehandle);
$shareparts = explode("|", $sharehandle);
if (count($shareparts) > 1) {
    $username = strtolower($shareparts[0]);
    $contentid = $shareparts[1];

    if (!is_dir("data/" . $username)) {
        gracefuldeath_json("user does not exist!");
    }

    //Make sure the file exists and can be loaded
    $jsondata = get_share_data($username, $config['readonlykey'], 'gracefuldeath_json');
    foreach ($jsondata['shares'] as $shares => $share) {
        if ($contentid == $share['guid'])
        {
            //add thumb
            if (strrpos($share['contenttype'], "image") !== false) {
                $share['content'] = make_url_from_contentid($share['guid'], $username, 'i');
                $share['thumbnail'] = make_url_from_contentid($share['guid'], $username, 'ithumb');
            } else {
                $share['thumbnail'] = make_url_from_contentid($share['guid'], $username, 'tthumb');
            }
            header('Content-Type: application/json');
            print_r(json_encode($share, JSON_PRETTY_PRINT));
        }
    }

} else {
    gracefuldeath_json("content request malformed!");
}


?>