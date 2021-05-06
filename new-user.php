<?php
include("common.php");
include("functions.php");
header('Content-Type: application/json');

//Make sure they sent a client id
$request_headers = get_request_headers();
if (array_key_exists('client-id', $request_headers) && in_array($request_headers['client-id'], $config['clientids'])) {
    //nothing to do
} else {
    gracefuldeath_json("no allowed client-id in request headers");
}

//Make sure we can get the input
$postjson = file_get_contents('php://input'); 
try {
    $postdata = json_decode($postjson);
}
catch (Exception $e) {
    gracefuldeath_json("invalid request payload: " . $e->getMessage());
}

if (isset($postdata->username) && $postdata->username != "" && isset($postdata->sharephrase) && $postdata->sharephrase != "" && isset($postdata->password) && $postdata->password != "") {
    echo(create_new_user($postdata->username, $postdata->sharephrase, $postdata->password, gracefuldeath_json));
} else {
    gracefuldeath_json("post data payload incomplete, missing username, sharephrase or password");
}
?>