<?php
global $config;
if ($config['allowhttps'] && !isset($_SERVER['HTTPS'])) {
    $secure_link = "https://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
    echo $actual_link;
    echo "<img src='images/lock.png' style='height:20px; width:20px; margin-top: -2px; vertical-align:middle'> <a href='" . $secure_link . "'>Switch to HTTPS</a> | ";
}
?>