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

function add_share_item($newshareitem, $oldsharedata, $username, $credential, $contenttype, $newid, $errorhandler = 'default_error_handler'){
    global $config;

    if (!isset($newshareitem) || !isset($oldsharedata) || !isset($credential) || !isset($contenttype) || !isset($newid)) {
        $errorhandler("functional call missing required parameters!");
        return;
    }
    if (($oldsharedata['sharephrase'] != $credential && base64_decode($oldsharedata['password']) != $credential) || $credential == $config['readonlykey']) {
        $errorhandler("not authorized: credentials not valid or read-only");
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
 
    //If number of share items exceeds maximum after this post, clean-up the overflow
    while (count($updatedsharedata['shares']) > $config['maxsharelength']) {
        remove_share_content($oldsharedata, $updatedsharedata['shares'][0]['guid'], $username);
        array_shift($updatedsharedata['shares']);
    }
    return $updatedsharedata;
}

function delete_share_item($itemid, $username, $credential, $errorhandler = 'default_error_handler') {
    //Make sure the file exists and can be loaded
    $jsondata = get_share_data($username, $credential, $errorhandler);

    //Load and return only the task list
    $updatedsharedata = remove_share_item($itemid, $jsondata, $username, $credential, $errorhandler);
    if (isset($updatedsharedata)) {
        $file = "data/" . strtolower($username) . "/sharelog.json";
        $written = file_put_contents($file, json_encode($updatedsharedata, JSON_PRETTY_PRINT));

        //Output the results
        if (!$written) {
            $errorhandler("failed to write to file " . $file);
        } else {
            return $updatedsharedata;
        }
    } else {
        $errorhandler("failed to build new share data");
    }
}

function remove_share_item($itemid, $oldsharedata, $username, $credential, $errorhandler = 'default_error_handler') {
    global $config;

    if (!isset($itemid) || !isset($oldsharedata) || !isset($credential)) {
        $errorhandler("functional call missing required parameters!");
        return;
    }
    if (base64_decode($oldsharedata['password']) != $credential || $credential == $config['readonlykey']) {
        $errorhandler("not authorized: credentials not valid or read-only");
        return;
    }

    remove_share_content($oldsharedata, $itemid, $username);
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

function remove_share_content($oldsharedata, $itemid, $username) {
    foreach ($oldsharedata['shares'] as $share => $value) {
        if ($itemid == $value['guid'])
        {
            if (strrpos($value['contenttype'], "image") !== false) {
                $file = $value['content'];
                if (file_exists($file)) {
                    unlink($file);
                }
                $file = str_replace($itemid, "thumb-".$itemid, $file);
                if (file_exists($file)) {
                    unlink($file);
                }
            }
        }
    }
}

function default_error_handler($error) {
    die ($error);
}

?>
