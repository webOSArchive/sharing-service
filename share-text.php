<?php
include("common.php");
include("functions.php");

header('Content-Type: application/json');

$auth = get_authorization();

//Make sure we can get the input
$postraw = file_get_contents('php://input', false, null, 0, $config['maxtextlength']); //TODO: better error for cropped text
if (!isset($postraw) || $postraw == "")
    gracefuldeath_json("no contentData in post payload");

$request_headers = get_request_headers();
if (!isset($request_headers["content-type"]) || $request_headers["content-type"] == "")
    gracefuldeath_json("content-type not specified");

$reqtype = explode(";", $request_headers["content-type"]);
$reqtype = $reqtype[0];

$newshareid = add_share_text($postraw, $auth['username'], $auth['credential'], $reqtype, 'gracefuldeath_json');

if (isset($newshareid)) {
    die ("{\"success\":\"" . make_url_from_contentid($newshareid, $auth['username'], "t") . "\"}");
} else {
    gracefuldeath_json("share text failed");
}

?>
