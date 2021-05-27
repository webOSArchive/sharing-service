<?php
    include("common.php");
    include("functions.php");

    if (isset($_POST["txtUsername"]) && isset($_POST["txtCredential"]) && isset($_POST["txtPassword"]))
    {
        $create_result = @create_new_user($_POST["txtUsername"], $_POST["txtCredential"], $_POST["txtPassword"], $_POST["txtCreateKey"], 'gracefuldeath_later');
        if (isset($create_result)) {
            setcookie("credential", $_POST["txtPassword"], time() + (3600), "/");
            header('Location: web-get-shares.php?username=' . $_POST["txtUsername"]);
        }
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
        function swapTech() {
            document.getElementById("imgNewWords").style.display = "inline";
        }

        function getNewWords() {
            if (typeof XMLHttpRequest === "function") {
                var xhr = new XMLHttpRequest();
                xhr.onreadystatechange = function() {
                    if (this.readyState === 4) {
                        document.getElementById("txtCredential").value = this.responseText;
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
<body class="login" onload="swapTech()">
<div class="login-header"><?php include("header.php");?><a href="index.php">Cancel</a>&nbsp;</div>
<table width="100%" height="95%" border="0" id="tableLayout">
    <tr>
        <td width="100%" height="100%" border="0" id="tdLayout" align="center">
            <?php
            if (isset($error_message)) {
            ?>
                <table class="tableBorder">
                    <tr>
                        <td>
                            <table width="100%" height="100%" bgcolor="white" border="0" class="tableOption">
                                <tr>
                                    <td colspan="3" align="center">
                                        <p>      
                                        <?php
                                            echo "<span style='color:red;'>Error: " . $error_message . "</span>";
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
                                    <p><img src="images/share-new.png" style="height: 64px; width: 64px; margin-top:8px; vertical-align:middle;" id="imgIcon"/>
                                    &nbsp;<b>Create your own Share Space</b></p>
                                    <p class="explainer">A Share space let's people share content with you from the web, or webOS mobile devices. Your user name is public, and will be used to make public links to content. Your Share Phrase is something you give to only those who you want to get content from -- if you only want to share with yourself, that's OK: just keep the Share Phrase private! And finally, your admin password is super private -- you can use it to change your settings, or delete content.</p>
                                    <form method="POST">
                                        <table style="margin: 18px;">
                                            <tr><td>User Name: </td><td><input type="text" name="txtUsername" id="txtUserName"></td></tr>
                                            <tr><td>Share Phrase:  </td><td><input type="text" id="txtCredential" name="txtCredential" value="<?php include("random-words.php"); ?>">&nbsp;<img src="images/refresh.png" id="imgNewWords" style="display:none; height:20px;width:20px; vertical-align:middle" onclick="getNewWords()"></td></tr>
                                            <tr><td>Admin Password: </td><td><input type="text" id="txtPassword" name="txtPassword"></td></tr>
                                            <?php
                                            if (isset($config["createkey"]) && $config["createkey"] != "") {
                                            ?>
                                                <tr><td>Create Key: </td><td><input type="text" id="txtCreateKey" name="txtCreateKey"></td></tr>
                                            <?php
                                            }
                                            ?>
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