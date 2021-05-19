<?php
include("common.php");
include("functions.php");

header('Content-Type: application/json');

$auth = get_authorization();

if (!isset($auth['credential']))
    gracefuldeath_json("no password in request");

//Look for an item to delete
if (!isset($_GET["itemid"])) {
    $request_headers = get_request_headers();
    if (array_key_exists('itemid', $request_headers)) {
        $itemid = $request_headers['itemid'];
    } else {
        gracefuldeath_json("no itemid to delete in request");
    }
} else {
    $itemid = $_GET["itemid"];
}

//Perform the deletion
$updatedsharedata = delete_share_item($itemid, $auth['username'], $auth['credential'], 'gracefuldeath_json');
if (isset($updatedsharedata)) {
    die ("{\"success\":\"share item ". $itemid ." deleted from " . $itemid . " share space\"}");
}

exit();
?>