<?php
    include("common.php");
    include("functions.php");
    $auth = array(
        'username' => $_POST['txtUsername'],
        'sharephrase' => $_POST['txtSharephrase'],
    );
    $error_message = null;

    if(isset($_POST['txtContent']) && $_POST['txtContent'] != "")
    {
        echo "Saving content: " . $_POST['txtContent'];
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
    <link rel="icon" href="images/icon.png" type="image/png">
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=1" />
    <meta http-equiv="pragma" content="no-cache">

    <script>
        
    </script>
</head>
<body class="login">
<table width="100%" height="100%" border="0" id="tableLayout">
    <tr>
        <td width="100%" height="100%" border="0" id="tdLayout" align="center">
        <?php
        if ((isset($imagePreview) && isset($imageDownload)) || isset($error_message)) {
        ?>
            <table width="800" border="1" class="tableBorder">
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
            <table width="800" height="400" border="1" class="tableBorder">
                <tr>
                    <td>
                        <table width="100%" height="100%" bgcolor="white" border="0" class="tableOption">
                            <tr>
                                <td colspan="3" align="center">
                                    <p><img src="images/share-text.png" style="height: 64px; width: 64px; margin-top:8px; vertical-align:middle;" id="imgIcon"/>
                                    &nbsp;<b>Share text</b></p>
                                    <form method="post" enctype="multipart/form-data">
                                        Enter the info for the person or service you want to share with, the type of text,<br>then type or paste the content to share...<br>
                                        <table style="margin: 18px;">
                                            <tr><td>User Name: </td><td><input type="text" name="txtUsername" id="txtUsername" value="<?php echo $_POST['txtUsername']?>"></td></tr>
                                            <tr><td>Share Phrase:  </td><td><input type="text" id="txtSharephrase" name="txtSharephrase" value="<?php echo $_POST['txtSharephrase']?>"></td></tr>
                                            <tr><td>Type: </td>
                                                <td>
                                                <select>
                                                    <option value="text">Text</option>
                                                    <option value="json">JSON</option>
                                                </select>
                                                </td>
                                            </tr>
                                        </table>
                                        <p>
                                                <textarea id="txtContent" name="txtContent" style="width: 600px; height: 280px;"></textarea>
                                        </p>
                                        <input type="submit" value="Share"><br/><br/><small><a href="index.php">Cancel</a></small>
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