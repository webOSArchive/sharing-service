<?php
include("common.php");

$sharehandle = $_SERVER['QUERY_STRING'];
if (!isset($sharehandle) || $sharehandle == "")
    graceful_death("content request not specified!");

$sharehandle = base64url_decode($sharehandle);
$shareparts = explode("|", $sharehandle);

if (count($shareparts) > 1) {
    $username = $shareparts[0];
    $contentid = $shareparts[1];

    if (!is_dir("data/" . $username)) {
        graceful_death("user does not exist!");
    }

    //Make sure the file exists and can be loaded
    $jsondata = get_share_data($username, $config['readonlykey'], graceful_death);
    foreach ($jsondata['shares'] as $share => $value) {
        //print_r($value);
        if ($contentid == $value['guid'])
        {
            if ($value['contenttype'] == "application/json") {
                header('Content-Type: application/json');
                print_r(json_encode($value['content']));
            }
            else {
                header('Content-Type: text/plain');
                print_r($value['content']);
            }
        }
    }

} else {
    graceful_death("content request malformed!");
}

function graceful_death($message) {
    die ($message);
}

?>