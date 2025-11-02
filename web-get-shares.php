<?php
if (!isset($_GET["username"]) || !isset($_COOKIE["credential"])) {
    header('Location: web-login.php?username=' . urlencode($_GET["username"]));
}
include("common.php");

$jsondata = get_share_data($_GET["username"], $_COOKIE["credential"], 'gracefuldeath_html');
$sharedata = convert_shares_to_public_schema($jsondata, $_GET["username"], $_COOKIE["credential"]);
if ($sharedata->accesslevel != 'admin') {
    header('Location: web-share-type.php?username=' . urlencode($_GET["username"]));
}
?>

<html>
<head>
    <title>Share Service</title>
    <?php include("web-meta.php") ?>

    <script>
        function swapTech() {
            document.getElementById("imgTogglePass").title = "click to reveal";
            document.getElementById("spnSharePhrase").style.display = "none";
        }

        function togglePassword(){
            var passBox = document.getElementById("spnSharePhrase");
            if(passBox.style.display == "inline")
                passBox.style.display = "none";
            else
                passBox.style.display = "inline";
        }
    </script>
</head>
<body class="login" onload="swapTech()">
<div class="login-header"><?php echo safe_html_output($_GET["username"]) ?> | <a href="index.php">Log Out</a>&nbsp;</div>
<div style="margin: 10px;">
<?php
if ($sharedata->accesslevel == 'admin') {
    ?>
    <div class="shareSpaceTitle">
        <b>Share Phrase: </b><img src="images/eyeball.png" id="imgTogglePass" style="display:inline;height:20px;width:20px;margin-top:-2px;vertical-align:middle" onclick="togglePassword()" title="<?php echo safe_html_output($sharedata->sharephrase); ?>">&nbsp;<span id="spnSharePhrase" style="display:none"><?php echo safe_html_output($sharedata->sharephrase); ?></span><br>
        <b>Share Type: </b><?php echo safe_html_output($sharedata->sharetype); ?>
    </div>
    <?php
    foreach($sharedata->shares as $thisshare)
    {
        echo "<table><tr>";
        switch ($thisshare['contenttype']) {
            case "text/plain":
                $textLink = make_url_from_contentid($thisshare['guid'], $_GET["username"], "t");
                $imageLoad = make_url_from_contentid($thisshare['guid'], $_GET["username"], "tthumb");
                echo "<td class='shareDescriptor'><img src='" . safe_html_output($imageLoad) . "' style='border:1px solid black; height: 64px' vertical-align:middle></td>";
                echo "<td class='shareContent'>Shared on: " . safe_html_output($thisshare['timestamp']) . " UTC <br>";
                echo "<div class='shareLinks'><b>Public View Link:</b> <a href='" . safe_html_output($textLink) . "' target='_blank'>" . safe_html_output($textLink) . "</a></div>";
                break;
            case "application/json":
                $textLink = make_url_from_contentid($thisshare['guid'], $_GET["username"], "t");
                $imageLoad = make_url_from_contentid($thisshare['guid'], $_GET["username"], "tthumb");
                echo "<td class='shareDescriptor'><img src='" . safe_html_output($imageLoad) . "' style='border:1px solid black; height: 64px' vertical-align:middle></td>";
                echo "<td class='shareContent'>Shared on: " . safe_html_output($thisshare['timestamp']) . " UTC <br>";
                echo "<div class='shareLinks'><b>Public View Link:</b> <a href='" . safe_html_output($textLink) . "' target='_blank'>" . safe_html_output($textLink) . "</a></div>";
                break;
            default:
                $imagePreview = make_url_from_contentid($thisshare['guid'], $_GET["username"], "image");
                $imageLoad = make_url_from_contentid($thisshare['guid'], $_GET["username"], "ithumb");
                $imageDownload = make_url_from_contentid($thisshare['guid'], $_GET["username"], "download");
                echo "<td class='shareDescriptor'><img src='" . safe_html_output($imageLoad) . "' style='height: 64px' vertical-align:middle></td>";
                echo "<td class='shareContent'>Shared on: " . safe_html_output($thisshare['timestamp']) . " UTC <br><div class='shareLinks'><b>Public View Link:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</b> <a href='" . safe_html_output($imagePreview) . "' target='_blank'>" . safe_html_output($imagePreview) . "</a><br>";
                echo "<b>Public Download Link:</b> <a href='" . safe_html_output($imageDownload) . "'>" . safe_html_output($imageDownload) . "</a></div>";
                break;
            }
        echo "<div class='shareDelete'><a href='web-delete-item.php?username=" . urlencode($_GET["username"]) . "&itemid=" . urlencode($thisshare['guid']) . "'>Delete</a></div>";
        echo "</td></tr></table>";
    }
} else {
    ?>
    <div class="shareSpaceTitle">
        <b>Wrong Access Level: </b>The credentials provided can only be used to share with this account.<br/>Login with the admin password to view previous shares.<br>
    </div>
<?php
}
?>

</div>
</body>
</html>
