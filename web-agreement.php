<?php
    setcookie("credential", "", time() - 3600, "/");
    include("common.php");
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
            <table width="800" height="400" border="1" class="tableBorder">
                <tr>
                    <td>
                        <table width="100%" height="100%" bgcolor="white" border="0" class="tableOption">
                            <tr>
                                <td colspan="3" align="center">
                                    <p><img src="images/icon-128.png" style="height: 64px; width: 64px; margin-top:8px; vertical-align:middle;" id="imgIcon"/>
                                    &nbsp;<b>Create your own Share Space</b></p>
                                    This Sharing Service is free to use, and free to host. If you want to host it yourself, visit the <a href="https://github.com/codepoet80/sharing-service">GitHub repo</a> for more information. If you want to use this hosted version, there are a few things you need to agree to...
                                        <div style="width: 80%; margin: 20px; text-align:left">
                                       <small >
                                        <?php
                                        echo file_get_contents("tandc.html");
                                        ?>
                                        </small>
                                        </div>
                                    <a href="web-new-user.php?agreed">I Agree</a> &nbsp;&nbsp; <a href="index.php">Disagree</a>
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