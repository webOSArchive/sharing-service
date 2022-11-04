<link rel="shortcut icon" sizes="256x256" href="images/icon-256.png">
<link rel="shortcut icon" sizes="196x196" href="images/icon-196.png">
<link rel="shortcut icon" sizes="128x128" href="images/icon-128.png">
<link rel="shortcut icon" href="favicon.ico">
<link rel="icon" type="image/png" href="images/icon.png" >
<link rel="apple-touch-icon" href="images/icon.png"/>
<link rel="apple-touch-startup-image" href="images/icon-256.png">
<meta name="apple-mobile-web-app-capable" content="yes" />
<meta name="apple-mobile-web-app-status-bar-style" content="white" />
<link rel="stylesheet" href="style.css">
<meta name="viewport" content="width=device-width, initial-scale=0.8, user-scalable=1" />
<meta http-equiv="pragma" content="no-cache">
<?php
  //Figure out what protocol the client wanted
  if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on')
    $protocol = "https";
  else
    $protocol = "http";
?>
<!-- Notification Code -->
<link rel="stylesheet" href="<?php echo $protocol ?>://www.webosarchive.org/notifications/notifications.css">
<script src="<?php echo $protocol ?>://www.webosarchive.org/notifications/notifications.js"></script>
<script src="<?php echo $protocol ?>://www.webosarchive.org/tldnotice.js"></script>
<!-- End Notification Code -->
