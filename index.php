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
</head>
<body class="login" style="margin: 0px;">
<div class="login-header"><a href="web-login.php">Login</a>&nbsp;</div>
<table width="100%" height="95%" border="0" id="tableLayout">
    <tr>
        <td width="100%" height="100%" border="0" id="tdLayout" align="center">
            <table width="800" height="400" border="1" class="tableBorder">
                <tr>
                    <td>
                        <table width="400" height="100%" bgcolor="white" border="0" class="tableOption">
                            <tr>
                                <td colspan="3" align="center">
                                    <a href="web-share-image.php">
                                    <img src="images/icon-128.png" style="margin-top:8px;" id="imgIcon"/><br/>
                                    <b>Share something new</b><br/>
                                    </a>
                                    &nbsp;
                                </td>
                            </tr>
                        </table>
                    </td>
                    <td>
                        <table width="400" height="100%" bgcolor="white" border="0" class="tableOption">
                            <tr>
                                <td colspan="3" align="center">
                                    <a href="web-agreement.php">
                                    <img src="images/icon-128.png" style="margin-top:8px;" id="imgIcon"/><br/>
                                    <b>Create a Share space</b><br/>
                                    </a>
                                    &nbsp;
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