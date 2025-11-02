<?php
    setcookie("credential", "", time() - 3600, "/");
    include("common.php");
    include("functions.php");
    header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
    header("Cache-Control: post-check=0, pre-check=0", false);
    header("Pragma: no-cache");

    //User logged in
    if (isset($_POST["txtUserName"]) && isset($_POST["txtCredential"]))
    {
        $login_result = @get_share_data($_POST["txtUserName"], $_POST["txtCredential"], 'gracefuldeath_later');
        if (isset($login_result)) {
            setcookie("credential", $_POST["txtCredential"], time() + (3600), "/");
            header('Location: web-get-shares.php?username=' . $_POST["txtUserName"]);
        }
    }
    //User was bounced here
    if (isset($_POST["txtUserName"]))
        $lastUsername = $_POST["txtUserName"];
    if (isset($_GET["username"]))
        $lastUsername = $_GET["username"];
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
                                    <p><img src="images/icon-128.png" style="height: 64px; width: 64px; margin-top:8px; vertical-align:middle;" id="imgIcon"/>
                                    &nbsp;<b>Log in to a Share Space</b></p>
                                    <p class="explainer">Enter the credentials for this Share space.<br>For read-only access, use the Share phrase, to make changes, use the Admin password.</p>
                                    <form method="POST">
                                        <table style="margin: 18px;">
                                            <tr><td>User Name: </td><td><input type="text" name="txtUserName" id="txtUserName" value="<?php if (isset($lastUsername)) { echo safe_html_output($lastUsername); } ?>"></td></tr>
                                            <tr><td>Password:  </td><td><input type="password" id="txtCredential" name="txtCredential">&nbsp;<img src="images/eyeball.png" id="imgTogglePass" style="display:none;height:20px;width:20px; vertical-align:middle" onclick="togglePassword()"></td></tr>
                                        </table>
                                        <input type="submit" value="Let's Go!">
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
