<?php
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

//This function gets the authorization data sent and validates that it exists, but does NOT perform the authorization
function get_authorization($errorhandler = gracefuldeath_json) {
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

    //Look for a sharephrase
    if (!isset($_GET["sharephrase"])){
        if (array_key_exists('share-phrase', $request_headers)) {
            $sharephrase = $request_headers['share-phrase'];
        } else {
            $errorhandler("no share-phrase in request");
        }
    }
    else {
        //check for encoding
        if (base64_encode(base64_decode($_GET["sharephrase"])) !== $_GET["sharephrase"]){
            $errorhandler("refusing to use querystring share-phrase in the clear: base64 encode and retry");
        }
        $sharephrase = $_GET["sharephrase"];
    }
    $sharephrase = strtolower($sharephrase);

    //Password is optional in authentication
    if (!isset($_GET["password"])){
        if (array_key_exists('Password', $request_headers)) {
            $password = $request_headers['Password'];
        }
    }
    else {
        if (base64_encode(base64_decode($$_GET["password"])) !== $$_GET["password"]){
            $errorhandler("refusing to use querystring password in the clear: base64 encode and retry");
        }
        $password = $_GET["password"];
    }
    if (isset($password))
        $password = base64_decode($password);

    return array(
        'username' => $username,
        'sharephrase' => $sharephrase,
        'password' => $password
    );
}

function get_share_data($username, $credential, $errorhandler = gracefuldeath_json) {
    global $config;
    $username = strtolower($username);
    $credential = strtolower($credential);

    $file = "data/" . $username . "/sharelog.json";
    if (!file_exists($file)) {
        $errorhandler("share user or service name not found or data file could not be opened.");
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
    if ($credential != $checkphrase && base64_encode($credential) != $adminpass && $credential != $config['readonlykey']) {
        $errorhandler("not authorized: credentials do not match any known key " . $credential);
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

function convert_shares_to_public_schema($data) {
    class userdata {};
    $thisuserdata = new userdata();
    $thisuserdata->shares = $data['shares'];
    return $thisuserdata;
}

function make_url_from_contentid($contentid, $user, $type) {
    global $config;
    
    switch ($type) {
        case "string":
            $functionName = "t";
            break;
        case "i":
            $functionName = "i";
            break;
        case "image":
            $functionName = "image";
            break;
        case "download":
            $functionName = "download";
            break;
        default:
            gracefuldeath_html("no valid type specified");
            return;
    }

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

function uniq_alphaid() {
    $mt = microtime(true);
    $mt =  $mt*100; //microsecs
    $ticks = (string)$mt; //100 Nanosecs
    return alphaID($ticks);
}

function alphaID($in, $to_num = false, $pad_up = false, $pass_key = null) {
    //https://kvz.io/create-short-ids-with-php-like-youtube-or-tinyurl.html
    $out   =   '';
    $index = 'abcdefghijklmnopqrstuvwxyz0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $base  = strlen($index);

    if ($pass_key !== null) {
    // Although this function's purpose is to just make the
    // ID short - and not so much secure,
    // with this patch by Simon Franz (https://blog.snaky.org/)
    // you can optionally supply a password to make it harder
    // to calculate the corresponding numeric ID

    for ($n = 0; $n < strlen($index); $n++) {
        $i[] = substr($index, $n, 1);
    }

    $pass_hash = hash('sha256',$pass_key);
    $pass_hash = (strlen($pass_hash) < strlen($index) ? hash('sha512', $pass_key) : $pass_hash);

    for ($n = 0; $n < strlen($index); $n++) {
        $p[] =  substr($pass_hash, $n, 1);
    }

    array_multisort($p, SORT_DESC, $i);
    $index = implode($i);
    }

    if ($to_num) {
    // Digital number  <<--  alphabet letter code
    $len = strlen($in) - 1;

    for ($t = $len; $t >= 0; $t--) {
        //$bcp = bcpow($base, $len - $t);
        $bcp = $base * ($len - $t);
        $out = $out + strpos($index, substr($in, $t, 1)) * $bcp;
    }

    if (is_numeric($pad_up)) {
        $pad_up--;

        if ($pad_up > 0) {
        $out -= pow($base, $pad_up);
        }
    }
    } else {
    // Digital number  -->>  alphabet letter code
    if (is_numeric($pad_up)) {
        $pad_up--;

        if ($pad_up > 0) {
        $in += pow($base, $pad_up);
        }
    }

    for ($t = ($in != 0 ? floor(log($in, $base)) : 0); $t >= 0; $t--) {
            //bcp = bcpow($base, $t);
            $bcp = $base * $t;
            $a   = floor($in / $bcp) % $base;
            $out = $out . substr($index, $a, 1);
            $in  = $in - ($a * $bcp);
        }
    }

    return $out;
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

?>
