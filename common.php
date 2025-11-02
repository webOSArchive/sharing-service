<?php
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

$config = include('config.php');

$supported_content_types = [
    "all",
    "image/gif",
    "image/jpeg",
    "image/png",
    "string",
    "text/plain",
    "application/json"
];

// Security helper function: Safely encode output for HTML context
function safe_html_output($string) {
    if ($string === null) {
        return '';
    }
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

// Security helper function: Validate file paths to prevent path traversal
function validate_file_path($filepath, $username = null) {
    // Get the absolute path and resolve any .. or . components
    $realPath = realpath($filepath);

    // If realpath returns false, the file doesn't exist yet (which might be OK for writes)
    // In that case, check the directory
    if ($realPath === false) {
        $dirPath = dirname($filepath);
        $realDirPath = realpath($dirPath);
        if ($realDirPath === false) {
            return false; // Directory doesn't exist
        }
        $realPath = $realDirPath . '/' . basename($filepath);
    }

    // Get the base data directory
    $baseDataDir = realpath(__DIR__ . '/data');

    // Ensure the file is within the data directory
    if (strpos($realPath, $baseDataDir) !== 0) {
        error_log("Path traversal attempt detected: " . $filepath);
        return false;
    }

    // If username is provided, ensure file is within that user's directory
    if ($username !== null) {
        $userDir = realpath(__DIR__ . '/data/' . strtolower($username));
        if ($userDir === false || strpos($realPath, $userDir) !== 0) {
            error_log("Path traversal attempt: file outside user directory: " . $filepath);
            return false;
        }
    }

    return true;
}

//This function gets the authorization data sent and validates that it exists, but does NOT perform the authorization
function get_authorization($errorhandler = 'gracefuldeath_json') {
    global $config;
    //Make sure they sent a client id
    $request_headers = get_request_headers();
    if (array_key_exists('client-id', $request_headers) && in_array($request_headers['client-id'], $config['clientids'])) {
        //nothing to do
    } else {
        $errorhandler("no allowed client-id in request headers");
    }

    //Look for a username
    if (!isset($_GET["username"]))
        $errorhandler("username not specified");
    $username = $_GET["username"];

    //Look for a credential
    if (!isset($_GET["credential"])){
        if (array_key_exists('credential', $request_headers)) {
            $credential = $request_headers['credential'];
        }
    }
    else {
        //check for encoding
        if (base64_encode(base64_decode($_GET["credential"])) !== $_GET["credential"]){
            $errorhandler("refusing to use querystring credential in the clear; encode and retry");
        }
        $credential = $_GET["credential"];
    }

    if (!isset($credential))
        $errorhandler("no credential in request");
    $credential = strtolower($credential);

    return array(
        'username' => $username,
        'credential' => $credential
    );
}

function get_share_data($username, $credential, $errorhandler = 'gracefuldeath_json') {
    global $config;
    $username = strtolower($username);
    $credential = strtolower($credential);

    $file = "data/" . $username . "/sharelog.json";
    if (!file_exists($file)) {
        $errorhandler("share user or service name not found or data file could not be opened: ". $username);
        return;
    }

    try {
        $sharedata = file_get_contents($file);
        $jsondata = json_decode($sharedata, true);
    }
    catch (exception $e) {
        $errorhandler("sharedata content could not be loaded");
        return;
    }

    //Make sure the file belongs to the requesting user
    $checkphrase = $jsondata['sharephrase'];
    $adminpass = $jsondata['password'];
    if ($credential != $checkphrase && !password_verify($credential, $adminpass) && $credential != $config['readonlykey']) {
        $errorhandler("credentials do not match any known key");
        return;
    }

    return $jsondata;
}

function get_request_headers() {
    //Cross platform way to get request headers, thanks to https://stackoverflow.com/a/20164575/8216691
    $request_headers = [];
    if (!function_exists('getallheaders')) {
        foreach ($_SERVER as $name => $value) {
            /* RFC2616 (HTTP/1.1) defines header fields as case-insensitive entities. */
            if (strtolower(substr($name, 0, 5)) == 'http_') {
                $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
            }
        }
        $request_headers = $headers;
    } else {
        foreach (getallheaders() as $name => $value) {
            $headers[strtolower($name)] = $value;
        }
        $request_headers = $headers;
    }
    return $request_headers;
}

function is_JSON($string){
    return is_string($string) && is_array(json_decode($string, true)) ? true : false;
}

function convert_shares_to_public_schema($data, $username, $credential) {
    class userdata {};
    $thisuserdata = new userdata();
    $thisuserdata->accesslevel = "share";
    if (password_verify(strtolower($credential), $data['password']))
        $thisuserdata->accesslevel = "admin";
    $thisuserdata->sharetype = $data['sharetype'];
    $thisuserdata->sharephrase = $data['sharephrase'];
    $thisuserdata->shares = array_reverse($data['shares']);
    //for each share, if its an image type, add a thumbnail
    $newShares = [];
    foreach ($thisuserdata->shares as $share) {
        if (strrpos($share['contenttype'], "image") !== false) {
            $share['content'] = make_url_from_contentid($share['guid'], $username, 'i');
            $share['thumbnail'] = make_url_from_contentid($share['guid'], $username, 'ithumb');
        } else {
            $share['thumbnail'] = make_url_from_contentid($share['guid'], $username, 'tthumb');
        }
        //Filter out types not allowed by the current share config
        if ($data['sharetype'] == "all" 
            || ($data['sharetype'] == "image" && strrpos($share['contenttype'], "image") !== false) 
            || ($data['sharetype'] == "string" && strrpos($share['contenttype'], "image") === false)
            || ($data['sharetype'] == $share['contenttype'])) {
                array_push($newShares, $share);
            }
    }
    $thisuserdata->shares = $newShares;

    return $thisuserdata;
}

function make_url_from_contentid($contentid, $user, $type) {
    global $config;

    $functionName = $type;

    if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on')   
        $url = "https://";
    else
        $url = "http://";
    if (isset($config['shorturl']) && $config['shorturl'] != "")
        $url.=$config['shorturl'];
    else
        $url.= $_SERVER['HTTP_HOST'];   
    $url.= $_SERVER['REQUEST_URI'];  
    $url = strtok($url, "?");
    $page = basename($_SERVER['PHP_SELF']);
    $url = str_replace($page, $functionName . ".php", $url);
    $sharehandle = base64url_encode($user . "|" . $contentid);
    $url = $url . "?" . $sharehandle;
    return $url;
}

function short_uniqid() {
    $newid = uniqid();      //eg: 6098638061285
    $newid = substr($newid, 6);
    $randletter = chr(97 + mt_rand(0, 25));
    return $randletter . $newid;
}

function base64url_encode($data){
  // First of all you should encode $data to Base64 string
  $b64 = base64_encode($data);

  // Make sure you get a valid result, otherwise, return FALSE, as the base64_encode() function do
  if ($b64 === false) {
    return false;
  }

  // Convert Base64 to Base64URL by replacing “+” with “-” and “/” with “_”
  $url = strtr($b64, '+/', '-_');

  // Remove padding character from the end of line and return the Base64URL result
  return rtrim($url, '=');
}

function base64url_decode($data, $strict = false){
  // Convert Base64URL to Base64 by replacing “-” with “+” and “_” with “/”
  $b64 = strtr($data, '-_', '+/');
  // Decode Base64 string and return the original data
  return base64_decode($b64, $strict);
}

function gracefuldeath_json($message) {
    if (!isset($message) || $message == "")
        $message = "unknown error occurred";
    die ("{\"error\":\"". $message ."\"}");
}

function gracefuldeath_html($error) {
    echo "ERROR: " . $error;
}

function gracefuldeath_httpcode($code) {
    $explanation = "An error has occured on the server and the request could not be processed";
    if (!isset($code))
        $code = 404;
    switch($code) {
        case 400: 
            $code = "400 Bad Request";
            $explanation = "The request was missing a required parameter and could not be processed";
            break;
        case 404:
            $code = "404 Not Found";
            $explanation = "The requested resource was not found or never existed";
            break;
        case 410:
            $code = "410 Gone";
            $explanation = "The requested content does not exist, it may have expired, been deleted, or you may have an out-of-date link";
            break;
        case 417:
            $code = "417 Expectation Failed";
            $explanation = "The user specified in the request was not found or never existed";
            break;
    }
    header($_SERVER["SERVER_PROTOCOL"] . $code);
    echo "<html><head>";
    echo "<meta name='viewport' content='width=device-width, initial-scale=1'>";
    echo "<title>Share Service - Error " . $code . "</title>";
    echo "<link rel='stylesheet' href='style-columns.css'>";
    include("web-meta.php");
    echo "</head>";
    echo "<body style='margin-left: 20px'><table border=0><tr>";
    echo "<td><img src='images/icon-128.png'></td>";
    echo "<td><h1>HTTP error: " . $code . "</h1></td>";
    echo "</tr></table>";
    echo "<p>" . $explanation . "<p>";
    echo "<a href='index.php'>Go Home</a>";
    echo "</body></html>";
    die;
}

function gracefuldeath_later($message) {
    global $error_message;
    $error_message = $message;
}

?>
