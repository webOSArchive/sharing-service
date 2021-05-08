<?php
include("common.php");
include("functions.php");

header('Content-Type: application/json');

$auth = get_authorization();

//Make sure we can get the input
$postraw = file_get_contents('php://input', false, null, 0, 5000); //TODO: make max size configurable
if (!isset($postraw) || $postraw == "")
    gracefuldeath_json("no data posted to share");

//Make sure the file exists and can be loaded
$jsondata = get_share_data($auth['username'], $auth['sharephrase'], gracefuldeath_json);

//Make sure this share content is valid and allowed
$allowedtype = $jsondata['sharetype'];
if (!isset($postraw) || $postraw == "") {
    gracefuldeath_json("no contentData in post payload");
}
$request_headers = get_request_headers();
if (isset($request_headers["content-type"]) && $request_headers["content-type"] != "") {
    $reqtype = explode(";", $request_headers["content-type"]);
    $reqtype = $reqtype[0];
    if (!in_array($reqtype, $supported_content_types))
        gracefuldeath_json("shared content-type, " . $reqtype . ", not supported by this service");  //TODO: could list
    if ($reqtype == "text/plain") {
        if ($allowedtype != "all" && $allowedtype != "string" && $allowedtype != "text/plain")
            gracefuldeath_json("shared content-type not allowed. this user or service instance only allows ". $allowedtype);
	else
	    $postdata = $postraw;
    }
    else if ($reqtype == "application/json") {
        if ($allowedtype != "all" && $allowedtype != "string" && $allowedtype != "application/json")
            gracefuldeath_json("shared content-type not allowed. this user or service instance only allows ". $allowedtype);
        else {
            //Make sure we're not getting junk
            if (!is_JSON($postraw)) {
                gracefuldeath_json("posted json could not be parsed: it may be too long or malformed");
            } else {
                $postdata = json_decode($postraw);
            }
        }
    }
    else {
        gracefuldeath_json("shared content-type not allowed on this endpoint");
    }
} else {
    gracefuldeath_json("content-type not specified");
}

//Get and update share file
$newid = uniqid();
$sharedata = get_share_data($auth['username'], $auth['sharephrase'], gracefuldeath_json);
$updatedsharedata = add_share_data($postdata, $sharedata, $auth['sharephrase'], $reqtype, $newid, gracefuldeath_json);
$file = "data/" . strtolower($auth['username']) . "/sharelog.json";
$written = file_put_contents($file, json_encode($updatedsharedata, JSON_PRETTY_PRINT));

//Output the results
if (!$written) {
    gracefuldeath_json("failed to write to file " . $file);
} else {
    echo "{\"success\":\"" . make_url_from_contentid($newid, $auth['username'], "string") . "\"}";
}
exit();

?>
