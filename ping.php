<?php
// ping - Simple health check endpoint, no authentication required
// Used by the Share Space app to verify a server is reachable before configuring it
include("common.php");
header("Content-Type: application/json");
echo json_encode(array(
    "status" => "ok",
    "version" => "2.0"
));
?>
