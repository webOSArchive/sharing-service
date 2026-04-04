<?php
    setcookie("credential", "", time() - 3600, "/");
    include("common.php");

    // Determine whether this is the community-hosted service or a self-hosted instance.
    // The community service shows webOS Archive branding and links.
    // Self-hosted instances show the site_name and welcome_message from config.php.
    $host = isset($_SERVER['HTTP_HOST']) ? strtolower($_SERVER['HTTP_HOST']) : '';
    $isCommunityService = (strpos($host, 'webosarchive.org') !== false
                        || strpos($host, 'wosa.link') !== false);

    $siteName = isset($config['site_name']) ? $config['site_name'] : 'Share Space';
    $welcomeMessage = isset($config['welcome_message']) ? $config['welcome_message'] : '';
?>

<!DOCTYPE html>
<html>
<head>
<title><?php echo safe_html_output($siteName); ?></title>
<link rel="stylesheet" href="style-columns.css">
<?php
    // Only load webOS Archive notification scripts on the community service.
    // Self-hosted instances do not depend on external webOS Archive infrastructure.
    if ($isCommunityService) {
        include("web-meta.php");
    } else {
?>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="shortcut icon" href="favicon.ico">
<link rel="stylesheet" href="style.css">
<?php } ?>
</head>
<body class="login">
<div class="login-header"><a href="web-login.php">Login</a>&nbsp;</div>

<table width="100%" height="95%" style="padding-bottom: 20px;"><tr><td width="100%" align="center" valign="middle">
<div style="max-width: 520px; margin-bottom: 20px; margin-top: 10px; padding-left: 24px; padding-right: 24px;">
    <?php
    // Show appropriate message based on platform and hosting context
    $client = strtolower(isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '');
    if (strpos($client, "hpwos") !== false || strpos($client, "webos") !== false) {
        echo "Welcome webOS User! This sharing service was made with you in mind, but this web front-end doesn't work on your current device. Instead, you can <a href='https://appcatalog.webosarchive.org/app/ShareSpace'>download the native webOS App called Share Space</a>, that works on Touchpad, Pre and all the other webOS phones!";
    } elseif ($isCommunityService) {
        echo "<p>This is the <b>webOS Archive</b> sharing service, provided for the webOS community. ";
        echo "Use the <a href='https://appcatalog.webosarchive.org/app/ShareSpace'>Share Space app</a> on your webOS device to connect, or manage your shares below.</p>";
    } else {
        echo "<h2>" . safe_html_output($siteName) . "</h2>";
        if ($welcomeMessage != '') {
            echo "<p>" . safe_html_output($welcomeMessage) . "</p>";
        }
    }
    ?>
</div>
<div id="container">

<div class="row">
  <div class="column">
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
                    <b>Create a Share Space</b><br/>
                    </a>
                    &nbsp;
                </td>
            </tr>
        </table>
   </div>
  </div>
</div>

</td></tr>
</table>
</div>

</body>
</html>
