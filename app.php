<?php
//find out what the requested app is
$req = explode('/', $_SERVER['REQUEST_URI']);
$query = end($req);
$path = "http://museum.weboslives.eu/AppPackages/" . $query;

//extract the app name
$app = explode("_", $query);
$app = $app[0];

//log the app request
$logURL = "https://appcatalog.webosarchive.org/WebService/countAppDownload.php?appid=" . $app . "&source=preware";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $logURL);
curl_setopt($ch, CURLOPT_HEADER, 0);
curl_exec($ch);
curl_close($ch);

//actually get the app and send it
$filename = $query;
$fp = fopen($path, 'rb');
header('Content-Description: File Transfer');
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="'.$filename.'"');
header('Expires: 0');
header('Cache-Control: must-revalidate');
header('Pragma: public');
ob_clean();
fpassthru($fp);
?>
