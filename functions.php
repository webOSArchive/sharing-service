<?php
function create_new_user($username, $sharephrase, $password, $createkey, $errorhandler = 'default_error_handler') {
    global $config;

    if (isset($config["createkey"]) && $config["createkey"] != "") {
        if (!isset($createkey) || $createkey == "" || strtolower($createkey) != strtolower($config["createkey"])){
            $errorhandler("a create key is required but was not found or did not match");
            return;
        }
    }
    if (isset($username) && $username != "" && isset($sharephrase) && $sharephrase != "" && isset($password) && $password != "") {

        //check for valid username
        $disallowed = array("con", "prn", "aux", "nul", "com", "do", "done", "elif", "else", "esac", "fi", "for", "function", "if", "in", "select", "then", "until", "while", "time");
        $username = strtolower($username);
        if (preg_match('/[^a-z]/', $username))
        {
            $errorhandler("username not valid - must be one word, alphabetic characters only");
            return;
        }
        if (in_array($username, $disallowed)) {
            $errorhandler("username not valid - must not be OS reserved word");
            return;
        }

        //check for valid sharephrase
        $sharephrase = strtolower($sharephrase);
        if (preg_match('/[^a-z ]/', $sharephrase))
        {
            $errorhandler("sharephrase not valid - use combinations provided by service");
            return;
        }

        //check for valid password
        $password = strtolower($password);
        if (preg_match('/[^A-Za-z0-9 ]/', $password))
        {
            $errorhandler("password not valid - use only letters, numbers and spaces");
            return;
        } else {
            $password = password_hash($password, PASSWORD_DEFAULT);
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
                $errorhandler("could not create user storage; check permissions on server.");
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
            $errorhandler("could not create sharelog in user storage; check for valid template and permissions on server");
            return;
        }
        return "{\"success\":\"new user created!\"}";
    } else {
        $errorhandler("post data payload incomplete, missing username, sharephrase or password");
        return;
    }
    return;
}

function add_share_text($postdata, $username, $credential, $reqtype, $errorhandler = 'default_error_handler') {
    global $config;
    global $supported_content_types;

    $postdata = strip_tags($postdata, $config['allowedhtml']);

    //Make sure the file exists and can be loaded
    $sharedata = get_share_data($username, $credential, $errorhandler);

    //Make sure this share content is valid and allowed
    $allowedtype = $sharedata['sharetype'];

    if (!in_array($reqtype, $supported_content_types)) {
        $errorhandler("shared content-type, " . $reqtype . ", not supported by this service");  //TODO: could list
    }
    if ($reqtype == "text/plain") {
        if ($allowedtype != "all" && $allowedtype != "string" && $allowedtype != "text/plain")
            $errorhandler("shared content-type not allowed; this user or service instance only allows ". $allowedtype);
    }
    else if ($reqtype == "application/json") {
        if ($allowedtype != "all" && $allowedtype != "string" && $allowedtype != "application/json")
            $errorhandler("shared content-type not allowed; this user or service instance only allows ". $allowedtype);
        else {
            //Make sure we're not getting junk
            if (!is_JSON($postdata)) {
                $errorhandler("posted json could not be parsed; it may be too long or malformed");
            } else {
                $postdata = json_decode($postdata);
            }
        }
    }
    else {
        $errorhandler("shared content-type not allowed on this endpoint");
    }

    //Get and update share file
    $newshareitem = make_share_item($postdata, $reqtype, short_uniqid());
    $updatedsharedata = add_share_item($newshareitem, $sharedata, $username, $credential, $errorhandler);

    if (isset($updatedsharedata)) {
        $file = "data/" . strtolower($username) . "/sharelog.json";
        $written = file_put_contents($file, json_encode($updatedsharedata, JSON_PRETTY_PRINT));

        //Output the results
        if (!$written) {
            $errorhandler("failed to write to file " . $file);
        } else {
            return $newshareitem;
        }
    } else {
        $errorhandler("failed to build new share data in add_share_text");
    }
}

function upload_share_file($username, $credential, $fileItem, $errorhandler) {
    global $config;
    global $supported_content_types;
    if($fileItem['name'])
    {
        if(!$fileItem['error'])
        {
            //Make sure the share exists and can be loaded
            $sharedata = get_share_data($username, $credential, $errorhandler);
            if ($sharedata) {

                $valid_file = true;
                //Make sure the uploaded image is allowed
                if($fileItem['size'] > ($config['maximagesize'])) {
                    $errorhandler("image is too large to share here; reduce the file size to be less than " . ($config['maximagesize'] / 1024000) . " MB");
                    $valid_file = false;
                }
                if (!in_array($fileItem['type'], $supported_content_types)) {
                    $errorhandler("upload file type, " . $fileItem['type'] . ", is not allowed by this user or service instance");
                    $valid_file = false;
                }
                $allowedtype = $sharedata['sharetype'];
                if ($allowedtype != "all" && strrpos($allowedtype, "image") === false)
                    $errorhandler("user or service instance does not allow images");
                if ($valid_file) {

                    $newid = short_uniqid();
                    $newfile = $newid . ".";
                    switch ($fileItem['type']){
                        case "image/gif":
                            $newfile = $newfile . "gif";
                            break;
                        case "image/jpeg":
                            $newfile = $newfile . "jpg";
                            break;
                        case "image/png":
                            $newfile = $newfile . "png";
                            break;
                    }
                    $newfile = "data/" . $username . "/" . $newfile;
                    
                    //Move the image into place
                    if (move_uploaded_file($fileItem['tmp_name'], $newfile)) {

                        //Make a new share item and add to users share file
                        $newshareitem = make_share_item($newfile, $fileItem['type'], $newid);
                        $updatedsharedata = add_share_item($newshareitem, $sharedata, $username, $credential, $errorhandler);
                        if (isset($updatedsharedata) && $updatedsharedata != "") {
                            $file = "data/" . strtolower($username) . "/sharelog.json";
                            $written = file_put_contents($file, json_encode($updatedsharedata, JSON_PRETTY_PRINT));
                            return $newshareitem;
                        } 
                    } else {
                        $errorhandler("could not move uploaded file to share directory; check server permissions");
                    }
                }
            }
        }
        else
        {
            $errorhandler("a server error occurred uploading file (code: " . $fileItem['error'] . "); file may be too big, or the server may be misconfigured.");
        }
    }
    return;
}

function make_share_item($itemcontent, $contenttype, $newid) {

    if (!isset($newid))
        $newid = short_uniqid();

    //calculate time stamp
    $now = new DateTime("now", new DateTimeZone("UTC"));
    $now = $now->format('Y-m-d H:i:s');

    $newshareentry = new stdClass();
    $newshareentry->guid = $newid;
    $newshareentry->contenttype = $contenttype;
    $newshareentry->content = $itemcontent;
    $newshareentry->timestamp = $now;

    return $newshareentry;
}

function add_share_item($newshareentry, $oldsharedata, $username, $credential, $errorhandler = 'default_error_handler') {
    global $config;
    if (!isset($newshareentry) || !isset($oldsharedata) || !isset($username) || !isset($credential)) {
        $errorhandler("add share item function call missing required parameters");
        return;
    }
    if (($oldsharedata['sharephrase'] != $credential && !password_verify($credential, $oldsharedata['password'])) || $credential == $config['readonlykey']) {
        $errorhandler("credentials not valid or read-only");
        return;
    }

    $updatedsharedata = $oldsharedata;
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
        $errorhandler("failed to delete new share data");
    }
}

function remove_share_item($itemid, $oldsharedata, $username, $credential, $errorhandler = 'default_error_handler') {
    global $config;

    if (!isset($itemid) || !isset($oldsharedata) || !isset($credential)) {
        $errorhandler("remove share item function call missing required parameters!");
        return;
    }
    if (!password_verify($credential, $oldsharedata['password']) || $credential == $config['readonlykey']) {
        $errorhandler("credentials not valid or read-only");
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
