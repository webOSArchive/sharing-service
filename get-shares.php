<?php
include("common.php");

header('Content-Type: application/json');

$auth = get_authorization();

//Make sure the file exists and can be loaded
$jsondata = get_share_data($auth['username'], $auth['credential'], 'gracefuldeath_json');

//Load and return only the task list
$sharedata = convert_shares_to_public_schema($jsondata, $auth['username'], $auth['credential']);
print_r (json_encode($sharedata));
exit();
?>