<?php
if (!isset($_GET["username"]) || !isset($_COOKIE["credential"])) {
    header('Location: web-login.php?username=' . $_GET["username"]);
} else {
    //echo "Welcome to your share space!<br>";
    //echo $_GET["username"] . "<br>";
    //echo $_COOKIE["credential"] . "<br>";
}
include("common.php");

//Make sure the file exists and can be loaded
$jsondata = get_share_data($_GET["username"], $_COOKIE["credential"], gracefuldeath_html);
//Load and return only the task list
$sharedata = convert_shares_to_public_schema($jsondata);
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
<body style="margin: 0px;">
<div class="login-header"><a href="index.php">Log Out</a>&nbsp;</div>
<div style="margin: 10px;">
<h1><img src="images/icon-128.png" style="height:64px;width:64px; vertical-align:middle">&nbsp; <?php echo $_GET["username"] ?> Share Space</h2>
<ul>
<?php

foreach($sharedata->shares as $thisshare)
{
     echo "<li>";
     print_r(json_encode($thisshare));
     echo "<br>&nbsp;<br></li>";

}

?>
</ul>
</div>
</body>
</html>