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
<body class="login">
<div class="login-header"><a href="index.php">Cancel</a>&nbsp;</div>
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
                                    <img src="images/share-image.png" style="margin-top:8px;" id="imgIcon"/><br/><br/>
                                    <b>Share an image</b><br/>
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
                                    <a href="web-share-text.php">
                                    <img src="images/share-text.png" style="margin-top:8px;" id="imgIcon"/><br/><br/>
                                    <b>Share some text or JSON</b><br/>
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