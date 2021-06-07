<?php
    setcookie("credential", "", time() - 3600, "/");
    include("common.php");
?>

<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Share Service</title>
<link rel="stylesheet" href="style-columns.css">
<?php include("web-meta.php") ?>
</head>
<body class="login">
<div class="login-header"><a href="web-login.php">Login</a>&nbsp;</div>

<table width="100%" height="95%" style="padding-bottom: 20px;"><tr><td width="100%"align="center" valign="middle">
<div style='max-width: 520px; color: dimgray; margin-bottom: 20px; margin-top: 10px; padding-left: 24px; padding-right: 24px; font-size: smaller'>
    <?php
    setcookie("firsttime", true, time() + 2592000, "/");
    if (strpos($client, "hpwos") || strpos($client, "webos")) {
        echo "Welcome webOS User! This sharing service was made for you in mind, but the web front-end doesn't work on your current device. Instead, you can <a href='http://appcatalog.webosarchive.com/showMuseumDetails.php?search=share+space&app=1005788'>download the native webOS App called Share Space</a>, that works on Touchpad, Pre and all the other webOS phones!";
    } else {
        if (!isset($_COOKIE["firsttime"]) || $_COOKIE["firsttime"] == true) {
            echo "The Share Service is a file sharing web app for retro devices -- but it works with modern devices too! You can create an account where you can share pictures or text with others, or they can share with you!";
        }
        setcookie("firsttime", false, time() + 2592000, "/");
    }
    ?>
</div>
<div id="container">

<div class="row">
  <div class="column" >
   <div class="columnContent">
        <table width="360" height="100%" border="0" class="tableOption">
            <tr>
                <td colspan="3" align="center">
                    <a href="web-share-type.php">
                    <img src="images/share-plus.png" style="margin-top:8px;" id="imgIcon" border="0"/><br/><br/>
                    <b>Share with someone</b><br/>
                    </a>
                    &nbsp;
                </td>
            </tr>
        </table>
   </div>
  </div>
  <div class="column">
   <div class="columnContent">
        <table width="360" height="100%" border="0" class="tableOption">
            <tr>
                <td colspan="3" align="center">
                    <a href="web-agreement.php">
                    <img src="images/share-new.png" style="margin-top:8px;" id="imgIcon" border="0"/><br/><br/>
                    <b>Create a Share space</b><br/>
                    </a>
                    &nbsp;
                </td>
            </tr>
        </table>
   </div>
  </div>

</td></tr>
</table>
</div>  
</div>

</body>
</html>
