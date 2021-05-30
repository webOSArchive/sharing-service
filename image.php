<?php
// image Endpoint
//      This endpoint loads a shared image into a webpage
function base64url_encode($data)
{
  $b64 = base64_encode($data);
  if ($b64 === false) {
    return false;
  }
  $url = strtr($b64, '+/', '-_');
  return rtrim($url, '=');
}
function is_valid_base64($str){
    if (base64_decode($str, true) !== false){
        return true;
    } else {
        return false;
    }
}

$source = $_SERVER['QUERY_STRING'];
//handle Facebook
if (strpos($source, "&fbclid")) {
    $source = str_replace("%3D", "=", $source);
    $stripFB = explode("&fbclid", $source);
    $source = $stripFB[0];
}

$link = "http://";
if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on')
    $link = "https://";
$link .= $_SERVER['HTTP_HOST'];
$link .= $_SERVER['REQUEST_URI'];
$linkParts = explode("image.php", $link);
$link = $linkParts[0] . "download.php?" . $source;

?>
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:fb="http://ogp.me/ns/fb#">

<head>
	<title>Image Proxy</title>
	<style>
		body { background-color: white; color:black; }
	</style>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
	<meta http-equiv="Pragma" content="no-cache" />
	<meta http-equiv="Expires" content="0" />
	<meta property="og:image" content="<?php echo $link ?>" />

</head>
<body>
<?php

if (is_valid_base64($source))
	$isource = $source;
else
	$isource = base64url_encode($source);
echo '<img src="i.php?'. $isource . '">';
?>
</body>
</html>
