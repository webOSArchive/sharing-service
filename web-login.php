<?php
    setcookie("credential", "", time() - 3600, "/");
    include("common.php");
    include("functions.php");
    header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
    header("Cache-Control: post-check=0, pre-check=0", false);
    header("Pragma: no-cache");

    if (isset($_POST["txtUserName"]) && isset($_POST["txtCredential"]))
    {
        $login_result = @get_share_data($_POST["txtUserName"], $_POST["txtCredential"], 'gracefuldeath_html');
        if (isset($login_result)) {
            setcookie("credential", $_POST["txtCredential"], time() + (3600), "/");
            header('Location: web-get-shares.php?username=' . $_POST["txtUserName"]);
        }
    }
    $lastUsername = $_POST["txtUserName"];
    if (isset($_GET["username"]))
        $lastUsername = $_GET["username"];
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
                                            <tr><td>User Name: </td><td><input type="text" name="txtUserName" id="txtUserName" value="<?php echo $lastUsername ?>"></td></tr>
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