<?php
include("common.php");

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

    //check for valid username
    $disallowed = array("con", "prn", "aux", "nul", "com", "do", "done", "elif", "else", "esac", "fi", "for", "function", "if", "in", "select", "then", "until", "while", "time");
    $username = strtolower($postdata->username);
    if (preg_match('/[^a-z]/', $username))
    {
        gracefuldeath_json("username not valid: must be one word, alphabetic characters only");
    }
    if (in_array($username, $disallowed)) {
        gracefuldeath_json("username not valid: must not be OS reserved word");
    }
    
    //check for valid sharephrase
    $sharephrase = strtolower($postdata->sharephrase);
    if (preg_match('/[^a-z ]/', $sharephrase))
    {
        gracefuldeath_json("sharephrase not valid: use combinations provided by service");
    }

    //check for valid password
    $password = strtolower($postdata->password);
    if (preg_match('/[^A-Za-z0-9 ]/', $password))
    {
        gracefuldeath_json("password not valid: use only letters, numbers and spaces");
    } else {
        $password = base64_encode($password);
    }

    //re-run the username uniqueness check
    if (file_exists("data/" . $username)) {
        gracefuldeath_json("user or service name already in use");
    }

    //try create the folder
    if (!is_dir("data")){
        gracefuldeath_json("data folder could not be found on server");
    } else {
        try {
            mkdir("data/" . $username);
        }
        catch (exception $e)
        {
            gracefuldeath_json("could not create user storage: check permissions on server.");
        }
    }

    //try create the data file
    try {
        //Load the template, populate this user's values, and save as a new file
        $newfile = "data/" . $username . "/sharelog.json";
        $templatefile = file_get_contents("sharelog-template.json"); 
        $templatedata = json_decode($templatefile);
        $templatedata->sharephrase = $sharephrase;
        $templatedata->password = $password;
        if (isset($postdata->sharetype)) {
            $req_sharetype = strtolower($postdata->sharetype);
            if (in_array($req_sharetype, $supported_content_types))
                $templatedata->sharetype = $postdata->sharetype;
        }
        $written = file_put_contents($newfile, json_encode($templatedata, JSON_PRETTY_PRINT));
    }
    catch (exception $e)
    {
        gracefuldeath_json("could not create sharelog in user storage: check for valid template and permissions on server");
    }

    echo "{\"success\":\"new user created!\"}";

} else {
    gracefuldeath_json("post data payload incomplete, missing username, sharephrase or password");
}
?>