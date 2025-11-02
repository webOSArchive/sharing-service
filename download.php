<?php
// Download Endpoint
//      This endpoint returns a shared image as a file that can be downloaded
include("common.php");

$sharehandle = $_SERVER['QUERY_STRING'];
if (!isset($sharehandle) || $sharehandle == "")
    gracefuldeath_httpcode(400);

//handle Facebook
if (strpos($sharehandle, "&fbclid")) {
    $sharehandle = str_replace("%3D", "=", $sharehandle);
    $stripFB = explode("&fbclid", $sharehandle);
    $sharehandle = $stripFB[0];
}
$sharehandle = base64url_decode($sharehandle);
$shareparts = explode("|", $sharehandle);
if (count($shareparts) > 1) {
    $username = strtolower($shareparts[0]);
    $contentid = $shareparts[1];

    if (!is_dir("data/" . $username)) {
        gracefuldeath_httpcode(417);
    }

    //Make sure the file exists and can be loaded
    $jsondata = get_share_data($username, $config['readonlykey'], 'gracefuldeath_httpcode');
    $found = false;
    foreach ($jsondata['shares'] as $share => $value) {
        //print_r($value);
        if ($contentid == $value['guid'])
        {
            $found = true;
            // Validate file path to prevent path traversal
            if (!validate_file_path($value['content'], $username)) {
                error_log("Path traversal attempt in download.php: " . $value['content']);
                gracefuldeath_httpcode(403);
            }
            $client = strtolower($_SERVER['HTTP_USER_AGENT']);
            if (strpos($client, "hpwos") || strpos($client, "webos") || strpos($client, "android")) {
                echo '<img src="i.php?'. safe_html_output($_SERVER['QUERY_STRING']) . '">';
            } else {
                header('Content-Description: File Transfer');
                header('Content-Type: application/octet-stream');
                header('Content-Disposition: attachment; filename="'.basename($value['content']).'"');
                header('Expires: 0');
                header('Cache-Control: must-revalidate');
                header('Pragma: public');
                header('Content-Length: ' . filesize($value['content']));
                flush(); // Flush system output buffer
                readfile($value['content']);
            }
        }
    }
    if (!$found) {
        gracefuldeath_httpcode(410);
    }

} else {
    gracefuldeath_httpcode(400);
}
?>
