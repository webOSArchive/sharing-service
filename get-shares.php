<?php
include("common.php");

header('Content-Type: application/json');

$auth = get_authorization();
//gracefuldeath_json("early death");
//Load share data
$sharedata = get_share_data($auth['username'], $auth['credential'], 'gracefuldeath_json');
//Convert to public schema
$sharedata = convert_shares_to_public_schema($sharedata, $auth['username'], $auth['credential']);
//Figure out if we're allowed to show this user
if ($sharedata->accesslevel == 'admin') {
    print_r (json_encode($sharedata));
} else {
    gracefuldeath_json("supplied credentials are only for sharing with this user; use admin password to view shares");
}
?>