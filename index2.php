<?php
    setcookie("credential", "", time() - 3600, "/");
    include("common.php");
?>

<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>webOS Share</title>
<script async defer data-domain="webosarchive.com" src="http://cloudpi.jonandnic.com/js/plausible.js"></script>
<link rel="shortcut icon" href="favicon.ico">
<link rel="stylesheet" href="style.css">
<link rel="icon" href="images/icon.png" type="image/png">
<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=1" />
<meta http-equiv="pragma" content="no-cache">

<style>
* {
  box-sizing: border-box;
}
html, body {
  font-family: Verdana,sans-serif;
  font-size: 14px;
  margin:0;
  padding:0;
  height:100%;
}
#container {
   min-height:100%;
   position:relative;
   max-width: 1000px;
}
h3 {
  margin-top:30px;
}
.table {
  border:0px; 
}

/* Create three equal columns that floats next to each other */
.column {
  float: left;
  width: 50%;
  display: flex;
  justify-content: center;
  min-height: 350px;
}

.tableOption {
    background-image: linear-gradient(white, dimgray);
}

/* Clear floats after the columns */
.row:after {
  content: "";
  display: table;
  clear: both;
}

/* Responsive layout - makes the three columns stack on top of each other instead of next to each other */
@media screen and (max-width: 800px) {
  .column {
    width: 100%;
  }
}
</style>
</head>
<body class="login">
<div class="login-header"><a href="web-login.php">Login</a>&nbsp;</div>

<table width="100%" height="95%" style="padding-bottom: 20px;"><tr><td width="100%" align="center" valign="middle">
<div id="container">
<div class="row" >
  <div class="column" >
   <div class="content" style="align-content: center; text-align: center;">
        <table width="400" height="100%" bgcolor="white" border="0" class="tableOption">
            <tr>
                <td colspan="3" align="center">
                    <a href="web-share-type.php">
                    <img src="images/share-plus.png" style="margin-top:8px;" id="imgIcon"/><br/><br/>
                    <b>Share with someone</b><br/>
                    </a>
                    &nbsp;
                </td>
            </tr>
        </table>
   </div>
  </div>
  <div class="column">
   <div class="content" style="align-content: center;  text-align: center;">
        <table width="400" height="100%" bgcolor="white" border="0" class="tableOption">
            <tr>
                <td colspan="3" align="center">
                    <a href="web-agreement.php">
                    <img src="images/share-new.png" style="margin-top:8px;" id="imgIcon"/><br/><br/>
                    <b>Create a Share space</b><br/>
                    </a>
                    &nbsp;
                </td>
            </tr>
        </table>
   </div>
  </div>
  <!--
  <div class="column" style="background-color:#ccc;">
   <div class="content" style="align-content: center;  text-align: center;">
   <h3>Help &amp; Documentation</h3><br>
   <a href="http://www.webosarchive.com/docs"><img src="help.png" style="margin-top:-10px"/></a>
   <p style="margin:30px;">Instructions on activating a device, now that its servers are offline, as well as an ongoing project to restore on-device Help and User documentation.</p>
   </div>
  </div>
    -->

</td></tr>
</table>
</div>  
</div>

</body>
</html>
