<?php
include("common.php");
include("functions.php");

header('Content-Type: application/json');

$auth = get_authorization();
print_r($auth);
die;
if (!isset($auth['password']))
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

//Make sure the file exists and can be loaded
$jsondata = get_share_data($auth['username'], $auth['credential'], 'gracefuldeath_json');

//Load and return only the task list
$updatedsharedata = remove_share_item($itemid, $jsondata, $auth['username'], $auth['password'], 'gracefuldeath_json');
if (isset($updatedsharedata)) {
    $file = "data/" . strtolower($auth['username']) . "/sharelog.json";
    $written = file_put_contents($file, json_encode($updatedsharedata, JSON_PRETTY_PRINT));

    //Output the results
    if (!$written) {
        gracefuldeath_json("failed to write to file " . $file);
    } else {
        echo "{\"success\":\"share item ". $itemid ." deleted from " . $auth['username'] . " share space\"}";
    }
} else {
    gracefuldeath_json("failed to build new share data");
}
exit();

print_r (json_encode($sharedata));
exit();
?>