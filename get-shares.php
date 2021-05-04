<?php
include("common.php");

header('Content-Type: application/json');

$auth = get_authorization();

//Make sure the file exists and can be loaded
$file = "data/" . ($auth['username'] . "/sharelog.json");
$jsondata = get_share_data($file, $auth['sharephrase'], gracefuldeath_json);

//Load and return only the task list
$sharedata = convert_shares_to_public_schema($jsondata);
print_r (json_encode($sharedata));
exit();
?>