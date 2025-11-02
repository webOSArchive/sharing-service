<?php
    include("common.php");
    include("functions.php");

    $username = "";
    if (isset($_POST['txtUsername']))
        $username = $_POST['txtUsername'];
    if (isset($_GET['username']))
        $username = $_GET['username'];

    $credential = "";
    if (isset($_POST['txtCredential']))
        $credential = $_POST['txtCredential'];
    if (isset($_COOKIE["credential"]))
        $credential = $_COOKIE["credential"];

    $auth = array(
        'username' => strtolower($username),
        'credential' => strtolower($credential),
    );
    $error_message = null;

    if ($_FILES && $_FILES['frmImage']) {
        $newImageItem = upload_share_file($auth['username'], $auth['credential'], $_FILES['frmImage'], 'gracefuldeath_later');
        if ($newImageItem) {
            $imageThumb = make_url_from_contentid($newImageItem->guid, $auth['username'], "ithumb");
            $imagePreview = make_url_from_contentid($newImageItem->guid, $auth['username'], "i");
            $imageDownload = make_url_from_contentid($newImageItem->guid, $auth['username'], "download");
        }
    }
?>

<html>
<head>
    <title>Share Service</title>
    <?php include("web-meta.php") ?>

    <script>
        function swapTech() {
            document.getElementById("imgTogglePass").style.display = "inline";
        }

        function togglePassword(){
            var passBox = document.getElementById("txtCredential");
            if(passBox.type == "text")
                passBox.type = "password";
            else
                passBox.type = "text";
        }
    </script>
</head>
<body class="login" onload="swapTech()">
<div class="login-header"><?php include("header.php");?><a href="index.php">Cancel</a>&nbsp;</div>
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
                                        echo "<span style='color:red;'>Error: " . safe_html_output($error_message) . "</span>";
                                    }
                                    else {
                                        echo "<a href='" . safe_html_output($imageDownload) . "'>";
                                        echo "<img src='" . safe_html_output($imageThumb) . "' style='height: 64px; margin-top:8px; vertical-align:middle;'>";
                                        echo "</a>";
                                        $imagePreview = str_replace("i.php", "image.php", $imagePreview);

                                        echo "&nbsp;<b>Image Shared!</b></p>";
                                        echo "<table style='margin: 18px;font-size: smaller;'>";
                                        echo "<tr>";
                                        echo "<td>Public View Link:</td><td> <span class='shareLinks'><a href='" . safe_html_output($imagePreview) . "' target='_blank'>" . safe_html_output($imagePreview) . "</a></span></tr>";
                                        echo "</tr><tr>";
                                        echo "<td>Public Download Link:</td><td> <span class='shareLinks'><a href='" . safe_html_output($imageDownload) . "'>" . safe_html_output($imageDownload) . "</a></span></td>";
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
                                            <tr><td>User Name: </td><td><input type="text" name="txtUsername" id="txtUsername" value="<?php echo safe_html_output($username) ?>"></td></tr>
                                            <tr><td>Share Phrase:  </td><td><input type="password" id="txtCredential" name="txtCredential" value="<?php echo safe_html_output($credential) ?>">&nbsp;<img src="images/eyeball.png" id="imgTogglePass" style="display:none;height:20px;width:20px; vertical-align:middle" onclick="togglePassword()"></td></tr>
                                            <tr><td>Photo: </td><td><input type="file" name="frmImage" accept="image/gif, image/jpeg, image/png" /></td></tr>
                                        </table>
                                        <input type="submit" value="Share">
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