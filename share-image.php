<?php
include("common.php");
include("functions.php");

header('Content-Type: application/json');

$auth = array(
    'username' => strtolower($_POST['username']),
    'credential' => strtolower($_POST['sharephrase']),
);

$error_message = null;
if ($_FILES['image']) {
    $newImageItem = upload_share_file($auth['username'], $auth['credential'], $_FILES['image'], 'gracefuldeath_json');
    if (isset($newImageItem)) {
        die(json_encode($newImageItem));
    }
}
?>