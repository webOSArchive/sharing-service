<?php
include("common.php");

$check_user = "";
if (isset($postdata->username)) {
    $check_user = $postdata->username;
}

if (isset($_GET['username'])) {
    $check_user = $_GET['username'];
}

$check_user = $check_user . ".json";

header('Content-Type: application/json');

if ($check_user != "" && $check_user != ".json") {
    if (file_exists("data/" . $check_user)) {
        echo "{\"error\":\"user or service name already in use\"}";
    } else {
        echo "{\"success\":\"user or service name is available\"}";
    }
} else {
    echo "{\"error\":\"no username found in get or post data\"}";
}

?>