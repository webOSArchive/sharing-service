<?php
    include("common.php");
    include("functions.php");
    $auth = array(
        'username' => $_POST['txtUsername'],
        'sharephrase' => $_POST['txtSharephrase'],
    );
    $error_message = null;

    if($_FILES['frmImage']['name'])
    {
        if(!$_FILES['frmImage']['error'])
        {
            //Make sure the share exists and can be loaded
            $sharedata = get_share_data($auth['username'], $auth['sharephrase'], gracefuldeath_later);
            if ($sharedata) {
                $newid = uniqid();

                $valid_file = true;
                //Make sure the uploaded image is allowed
                if($_FILES['frmImage']['size'] > ($config['maximagesize'])) {
                    gracefuldeath_later("Your image is too large to share here. Please reduce the file size to be less than " . ($config['maximagesize'] / 1024000) . " MB");
                    $valid_file = false;
                }
                if (!in_array($_FILES['frmImage']['type'], $supported_content_types)) {
                    gracefuldeath_later('Oops! That file type is not allowed');
                    $valid_file = false;
                }
                if ($valid_file) {

                    $newfile = $newid . ".";
                    switch ($_FILES['frmImage']['type']){
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
                    $newfile = "data/" . $auth['username'] . "/" . $newfile;
                    
                    //Move the image into place
                    $postdata = $newfile;
                    if (move_uploaded_file($_FILES['frmImage']['tmp_name'], $newfile)) {
                        //Add a record to the user's share data
                        $updatedsharedata = add_share_data($postdata, $sharedata, $auth['sharephrase'], $_FILES['frmImage']['type'], $newid, gracefuldeath_later);
                        if (isset($updatedsharedata) && $updatedsharedata != "") {
                            $file = "data/" . strtolower($auth['username']) . "/sharelog.json";
                            $written = file_put_contents($file, json_encode($updatedsharedata, JSON_PRETTY_PRINT));
                        }

                        if ($written) {
                            $imagePreview = make_url_from_contentid($newid, $auth['username'], "i");
                            $imageDownload = make_url_from_contentid($newid, $auth['username'], "download");
                        }
                    } else {
                        gracefuldeath_later("Could not move uploaded file to share directory. Check server permissions.");
                    }
                }
            }
        }
        else
        {
            gracefuldeath_later("A server error occurred uploading your file (Code: " . $_FILES['frmImage']['error'] . ")<br>Your file may be too big, or the server may be misconfigured.");
        }
    } 

    function gracefuldeath_later($message) {
        global $error_message;
        $error_message = $message;
    }
?>

<html>
<head>
    <title>webOS Share</title>
    <link rel="shortcut icon" href="favicon.ico">
    <link rel="stylesheet" href="style.css">
    <?php include("web-meta.php") ?>
</head>
<body class="login">
<div class="login-header"><a href="index.php">Cancel</a>&nbsp;</div>
<table width="100%" height="95%" border="0" id="tableLayout">
    <tr>
        <td width="100%" height="100%" border="0" id="tdLayout" align="center">
        <?php
        if ((isset($imagePreview) && isset($imageDownload)) || isset($error_message)) {
        ?>
            <table class="tableBorder">
                <tr>
                    <td>
                        <table width="100%" height="100%" bgcolor="white" border="0" class="tableOption">
                            <tr>
                                <td colspan="3" align="center">
                                    <p>      
                                    <?php
                                    if (isset($error_message)) {
                                        echo "<span style='color:red;'>Error: " . $error_message . "</span>";
                                    }
                                    else {
                                        echo "<a href='" . $imageDownload . "'>";
                                        echo "<img src='" . $imagePreview . "' style='height: 64px; margin-top:8px; vertical-align:middle;'>";  
                                        echo "</a>";
                                        $imagePreview = str_replace("i.php", "image.php", $imagePreview);
                                    
                                        echo "&nbsp;<b>Image Shared!</b></p>";
                                        echo "<table style='margin: 18px;font-size: smaller;'>";
                                        echo "<tr>";
                                        echo "<td>Public View Link:</td><td> <span class='shareLinks'><a href='" . $imagePreview . "'>" . $imagePreview . "</a></span></tr>";
                                        echo "</tr><tr>";
                                        echo "<td>Public Download Link:</td><td> <span class='shareLinks'><a href='" . $imageDownload . "'>" . $imageDownload . "</a></span></td>";
                                        echo "</tr>";
                                        echo "</table>";
                                    }
                                    ?>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        <?php
        }
        ?>
            <table class="tableBorder">
                <tr>
                    <td>
                        <table width="100%" height="100%" bgcolor="white" border="0" class="tableOption">
                            <tr>
                                <td colspan="3" align="center">
                                    <p><img src="images/share-image.png" style="height: 64px; width: 64px; margin-top:8px; vertical-align:middle;" id="imgIcon"/>
                                    &nbsp;<b>Share an Image</b></p>
                                    <form method="post" enctype="multipart/form-data">
                                        <div class="pageExplainer">
                                        Enter the info for the person or service you want to share with, then pick an image to share with them...<br>
                                        </div>
                                        <table style="margin: 18px;">
                                            <tr><td>User Name: </td><td><input type="text" name="txtUsername" id="txtUsername" value="<?php echo $_POST['txtUsername']?>"></td></tr>
                                            <tr><td>Share Phrase:  </td><td><input type="text" id="txtSharephrase" name="txtSharephrase" value="<?php echo $_POST['txtSharephrase']?>"></td></tr>
                                            <tr><td>Photo: </td><td><input type="file" name="frmImage" accept="image/gif, image/jpeg, image/png" /></td></tr>
                                        </table>
                                        <input type="submit" value="Share"><br/><br/><span class='cancel'><a href="index.php">Cancel</a></span>
                                    </form>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
</body>
</html> 