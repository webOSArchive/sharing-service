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

    if(isset($_POST['txtContent']) && $_POST['txtContent'] != "")
    {
        if (isset($_POST['optContentType'])) {
            $newshare = add_share_text($_POST['txtContent'], $auth['username'], $auth['credential'], $_POST['optContentType'], 'gracefuldeath_later');
            if (isset($newshare)) {
                $textThumb = make_url_from_contentid($newshare->guid, $auth['username'], "tthumb");
                $textPreview = make_url_from_contentid($newshare->guid, $auth['username'], "t");
            }
        } else {
            gracefuldeath_later("Content-Type not specified");
        }
    }
?>

<html>
<head>
    <title>webOS Share</title>
    <link rel="shortcut icon" href="favicon.ico">
    <link rel="stylesheet" href="style.css">
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
<div class="login-header"><a href="index.php">Cancel</a>&nbsp;</div>
<table width="100%" height="95%" border="0" id="tableLayout">
    <tr>
        <td width="100%" height="100%" border="0" id="tdLayout" align="center">
        <?php
        if ((isset($textThumb) && isset($textPreview)) || isset($error_message)) {
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
                                        echo "<a href='" . $textPreview . "'>";
                                        echo "<img src='" . $textThumb . "' style='border: 1px solid black; height: 64px; margin-top:8px; vertical-align:middle;'>";  
                                        echo "</a>";
                                    
                                        echo "&nbsp;<b>Text Shared!</b></p>";
                                        echo "<table style='margin: 18px;font-size: smaller;'>";
                                        echo "<tr>";
                                        echo "<td>Public View Link:</td><td> <span class='shareLinks'><a href='" . $textPreview . "' target='_blank'>" . $textPreview . "</a></span></tr>";
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
                                    <p><img src="images/share-text.png" style="height: 64px; width: 64px; margin-top:8px; vertical-align:middle;" id="imgIcon"/>
                                    &nbsp;<b>Share text</b></p>
                                    <form method="post" enctype="multipart/form-data">
                                        <div class="pageExplainer">
                                        Enter the info for the person or service you want to share with, the type of text, then type or paste the content to share...
                                        </div>
                                        <table style="margin: 18px;">
                                            <tr><td>User Name: </td><td><input type="text" name="txtUsername" id="txtUsername" value="<?php echo $username ?>"></td></tr>
                                            <tr><td>Share Phrase:  </td><td><input type="password" id="txtCredential" name="txtCredential" value="<?php echo $credential ?>">&nbsp;<img src="images/eyeball.png" id="imgTogglePass" style="display:none;height:20px;width:20px; vertical-align:middle" onclick="togglePassword()"></td></tr>
                                            <tr><td>Type: </td>
                                                <td>
                                                <select id="optContentType" name="optContentType">
                                                    <option value="text/plain">Text</option>
                                                    <option value="application/json">JSON</option>
                                                </select>
                                                </td>
                                            </tr>
                                        </table>
                                        <p>
                                                <textarea id="txtContent" name="txtContent" style="width: 100%; height: 280px;"></textarea>
                                        </p>
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