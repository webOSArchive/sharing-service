<?php
    include("common.php");
?>

<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Share Service</title>
    <?php include("web-meta.php") ?>
    <link rel="stylesheet" href="style-columns.css">
</head>
<body class="login">
<div class="login-header"><a href="web-login.php">Cancel</a>&nbsp;</div>

<table width="100%" height="95%" style="padding-bottom: 20px;"><tr><td width="100%" align="center" valign="middle">
<div id="container">
<div class="row" >
  <div class="column" >
   <div class="columnContent">
        <table width="360" height="100%" border="0" class="tableOption">
            <tr>
                <td colspan="3" align="center">
                    <a href="web-share-image.php?<?php echo $_SERVER['QUERY_STRING']?>">
                    <img src="images/share-image.png" style="margin-top:8px;" id="imgIcon" border="0"/><br/><br/>
                    <b>Share an image</b><br/>
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
                    <a href="web-share-text.php?<?php echo $_SERVER['QUERY_STRING']?>">
                    <img src="images/share-text.png" style="margin-top:8px;" id="imgIcon" border="0"/><br/><br/>
                    <b>Share some text or JSON</b><br/>
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
