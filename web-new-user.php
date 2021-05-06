<?php
    //setcookie("grandmaster", "", time() - 3600);
    include("common.php");
    include("functions.php");
    header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
    header("Cache-Control: post-check=0, pre-check=0", false);
    header("Pragma: no-cache");

    if (isset($_POST["txtUserName"]) && isset($_POST["txtSharephrase"]) && isset($_POST["txtPassword"]))
    {
        $create_result = @create_new_user($_POST["txtUserName"], $_POST["txtUserName"], $_POST["txtUserName"], gracefuldeath_html);
        if (isset($create_result)) {
            setcookie("sharephrase", $_POST["txtSharephrase"], time() + (3600), "/");
            setcookie("password", $_POST["txtPassword"], time() + (3600), "/");
            header('Location: web-get-shares.php?username=' . $_POST["txtUserName"]);
        }
    } 

    function gracefuldeath_html($error) {
        echo "ERROR: " . $error;
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
        function getNewWords() {
            if (typeof XMLHttpRequest === "function") {
                var xhr = new XMLHttpRequest();
                xhr.onreadystatechange = function() {
                    if (this.readyState === 4) {
                        document.getElementById("txtSharephrase").value = this.responseText;
                    }
                };
                xhr.open("GET", "random-words.php");
                xhr.send();
            } else {
                document.location.reload();
            }
        }
    </script>

</head>
<body class="login">


<table width="100%" height="100%" border="0" id="tableLayout">
    <tr>
        <td width="100%" height="100%" border="0" id="tdLayout" align="center">
            <table width="800" height="400" border="1" class="tableBorder">
                <tr>
                    <td>
                        <table width="100%" height="100%" bgcolor="white" border="0" class="tableOption">
                            <tr>
                                <td colspan="3" align="center">
                                    <p><img src="images/icon-128.png" style="height: 64px; width: 64px; margin-top:8px; vertical-align:middle;" id="imgIcon"/>
                                    &nbsp;<b>Create your own Share Space</b></p>
                                    <p class="explainer">A Share space let's people share content with you from the web, or webOS mobile devices. Your user name is public, and will be used to make public links to content. Your Share Phrase is something you give to only those who you want to get content from -- if you only want to share with yourself, that's OK: just keep the Share Phrase private! And finally, your admin password is super private -- you can use it to change your settings, or delete content.</p>
                                    <form method="POST">
                                        <table style="margin: 18px;">
                                            <tr><td>User Name: </td><td><input type="text" name="txtUserName" id="txtUserName"></td></tr>
                                            <tr><td>Share Phrase:  </td><td><input type="text" id="txtSharephrase" name="txtSharephrase" value="<?php include("random-words.php"); ?>">&nbsp;<img src="images/refresh.png" style="height:20px;width:20px; vertical-align:middle" onclick="getNewWords()"></td></tr>
                                            <tr><td>Admin Password: </td><td><input type="text" id="txtPassword" name="txtPassword"></td></tr>
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