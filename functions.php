<?php
function create_new_user($username, $sharephrase, $password, $errorhandler = 'default_error_handler') {
    if (isset($username) && $username != "" && isset($sharephrase) && $sharephrase != "" && isset($password) && $password != "") {

        //check for valid username
        $disallowed = array("con", "prn", "aux", "nul", "com", "do", "done", "elif", "else", "esac", "fi", "for", "function", "if", "in", "select", "then", "until", "while", "time");
        $username = strtolower($username);
        if (preg_match('/[^a-z]/', $username))
        {
            $errorhandler("username not valid: must be one word, alphabetic characters only");
            return;
        }
        if (in_array($username, $disallowed)) {
            $errorhandler("username not valid: must not be OS reserved word");
            return;
        }

        //check for valid sharephrase
        $sharephrase = strtolower($sharephrase);
        if (preg_match('/[^a-z ]/', $sharephrase))
        {
            $errorhandler("sharephrase not valid: use combinations provided by service");
            return;
        }

        //check for valid password
        $password = strtolower($password);
        if (preg_match('/[^A-Za-z0-9 ]/', $password))
        {
            $errorhandler("password not valid: use only letters, numbers and spaces");
            return;
        } else {
            $password = base64_encode($password);
        }

        //re-run the username uniqueness check
        if (file_exists("data/" . $username)) {
            $errorhandler("user or service name already in use");
            return;
        }

        //try create the folder
        if (!is_dir("data")){
            $errorhandler("data folder could not be found on server");
            return;
        } else {
            try {
                mkdir("data/" . $username);
            }
            catch (exception $e)
            {
                $errorhandler("could not create user storage: check permissions on server.");
                return;
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
            if (isset($sharetype)) {
                $req_sharetype = strtolower($sharetype);
                if (in_array($req_sharetype, $supported_content_types))
                    $templatedata->sharetype = $sharetype;
            }
            $written = file_put_contents($newfile, json_encode($templatedata, JSON_PRETTY_PRINT));
        }
        catch (exception $e)
        {
            $errorhandler("could not create sharelog in user storage: check for valid template and permissions on server");
            return;
        }
        return "{\"success\":\"new user created!\"}";
    } else {
        $errorhandler("post data payload incomplete, missing username, sharephrase or password");
        return;
    }
    return;
}

function add_share_item($newshareitem, $oldsharedata, $sharephrase, $contenttype, $newid, $errorhandler = 'default_error_handler'){
    global $config;

    if (!isset($newshareitem) || !isset($oldsharedata) || !isset($sharephrase) || !isset($contenttype) || !isset($newid)) {
        $errorhandler("functional call missing required parameters!");
        return;
    }
    if ($oldsharedata['sharephrase'] != $sharephrase || $sharephrase == $config['readonlykey']) {
        $errorhandler("not authorized: sharephrase not valid or read-only");
        return;
    }

    //calculate time stamp
    $now = new DateTime("now", new DateTimeZone("UTC"));
    $now = $now->format('Y-m-d H:i:s');

    $updatedsharedata = $oldsharedata;
    $newshareentry = new stdClass();
    $newshareentry->guid = $newid;
    $newshareentry->contenttype = $contenttype;
    $newshareentry->content = $newshareitem;
    $newshareentry->timestamp = $now;
    array_push($updatedsharedata['shares'], $newshareentry);
    //TODO: If number of share items is longer than allowed, pop oldest item

    return $updatedsharedata;
}

function remove_share_item($itemid, $oldsharedata, $password, $errorhandler = 'default_error_handler') {
    global $config;

    if (!isset($itemid) || !isset($oldsharedata) || !isset($password)) {
        $errorhandler("functional call missing required parameters!");
        return;
    }
    if (base64_decode($oldsharedata['password']) != $password || $password == $config['readonlykey']) {
        $errorhandler("not authorized: password not valid or read-only");
        return;
    }

    $newShares = [];
    foreach ($oldsharedata['shares'] as $share => $value) {
        if ($itemid != $value['guid'])
        {
            array_push($newShares, $value);
        }
    }

    $updatedsharedata = $oldsharedata;
    $updatedsharedata['shares'] = $newShares;
    return $updatedsharedata;
}

function default_error_handler($error) {
    die ($error);
}

?>
