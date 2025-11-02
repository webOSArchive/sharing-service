<?php
// T (Text) Endpoint
//      This endpoint supports returning the content of a text or JSON share
include("common.php");

$sharehandle = $_SERVER['QUERY_STRING'];
if (!isset($sharehandle) || $sharehandle == "")
    gracefuldeath_httpcode(400);

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
            if ($value['contenttype'] == "application/json") {
                header('Content-Type: application/json');
                print_r(json_encode($value['content'], JSON_PRETTY_PRINT));
            }
            else {
                header('Content-Type: text/html');
                // First encode all HTML entities for security
                $content = safe_html_output($value['content']);
                // Then convert URLs to hyperlinks on the encoded content
                print_r(turnUrlIntoHyperlink($content));
            }
        }
    }
    if (!$found) {
        gracefuldeath_httpcode(410);
    }

} else {
    gracefuldeath_httpcode(400);
}

function turnUrlIntoHyperlink($string){
    //https://stackoverflow.com/questions/23366790/php-find-all-links-in-the-text
    //The Regular Expression filter
    $reg_exUrl = "/(?i)\b((?:https?:\/\/|www\d{0,3}[.]|[a-z0-9.\-]+[.][a-z]{2,4}\/)(?:[^\s()<>]+|\(([^\s()<>]+|(\([^\s()<>]+\)))*\))+(?:\(([^\s()<>]+|(\([^\s()<>]+\)))*\)|[^\s`!()\[\]{};:'\".,<>?«»""'']))/";

    // Check if there is a url in the text
    if(preg_match_all($reg_exUrl, $string, $url)) {
        // Loop through all matches
        foreach($url[0] as $newLinks){
            // Decode the URL since the string is already HTML-encoded
            $decodedLink = html_entity_decode($newLinks, ENT_QUOTES, 'UTF-8');
            if(strstr($decodedLink, ":" ) === false){
                $link = 'http://'.$decodedLink;
            }else{
                $link = $decodedLink;
            }
            // Validate URL scheme to prevent javascript: and data: URLs
            $parsedUrl = parse_url($link);
            if (isset($parsedUrl['scheme']) && !in_array(strtolower($parsedUrl['scheme']), ['http', 'https', 'ftp'])) {
                continue; // Skip invalid/dangerous schemes
            }
            // Create Search and Replace strings with proper encoding
            $search  = $newLinks;
            $replace = '<a href="'.safe_html_output($link).'">'.safe_html_output($decodedLink).'</a>';
            $string = str_replace($search, $replace, $string);
        }
    }
    return $string;
}


?>
