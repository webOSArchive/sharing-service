<?php
    setcookie("grandmaster", "", time() - 3600);
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
        function swapTech() {
            document.getElementById("divNewUser").innerHTML = "<input class=\"button\" type=\"button\" id=\"btnNew\" value=\"New Game\" onclick=\"document.location='agreement.php'\"/>";
            document.getElementById("imgIcon").src = "images/icon.png";
        }
        function checkSubmit() {
            if (document.getElementById("txtMove").value == "") {
                document.getElementById("txtMove").focus();
                return false;
            }
            if (document.getElementById("txtGrandmaster").value == "") {
                document.getElementById("txtGrandmaster").focus();
                return false;
            }
            return true;
        }
    </script>
</head>
<body backgroundOff='images/chessboard.jpg' class="login" onload="swapTech()">
<table width="100%" height="100%" border="0" id="tableLayout">
    <tr>
        <td width="100%" height="100%" border="0" id="tdLayout" align="center">
            <form method="POST" action="tasks.php" onsubmit="return checkSubmit()">
                <table width="800" height="400" border="1" class="tableBorder">
                    <tr>
                        <td>
                            <table width="400" height="100%" bgcolor="white" border="0" class="tableOption">
                                <tr>
                                    <td colspan="3" align="center">
                                        <img src="images/icon-128.png" style="margin-top:8px;" id="imgIcon"/><br/>
                                        <b>Share with someone else</b><br/>
                                        &nbsp;
                                    </td>
                                </tr>
                            </table>
                        </td>
                        <td>
                            <table width="400" height="100%" bgcolor="white" border="0" class="tableOption">
                                <tr>
                                    <td colspan="3" align="center">
                                        <img src="images/icon-128.png" style="margin-top:8px;" id="imgIcon"/><br/>
                                        <b>Create your own Share space</b><br/>
                                        &nbsp;
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </form>
        </td>
    </tr>
</table>
</body>
</html> 