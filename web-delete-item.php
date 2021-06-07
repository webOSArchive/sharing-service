<?php
    if (!isset($_GET["username"]) || !isset($_COOKIE["credential"])) {
        header('Location: web-login.php?username=' . $_GET["username"]);
    }
    include("common.php");
    include("functions.php");
    $auth = array(
        'username' => strtolower($_GET['username']),
        'credential' => strtolower($_COOKIE["credential"]),
    );
    $error_message = null;

    //Look for an item to delete
    if (!isset($_GET["itemid"])) {
        $request_headers = get_request_headers();
        if (array_key_exists('itemid', $request_headers)) {
            $itemid = $request_headers['itemid'];
        } else {
            gracefuldeath_html("no itemid to delete in request");
        }
    } else {
        $itemid = $_GET["itemid"];
    }
    //Perform the deletion
    $updatedsharedata = delete_share_item($itemid, $auth['username'], $auth['credential'], 'gracefuldeath_json');
?>

<html>
<head>
    <title>Share Service</title>
    <?php include("web-meta.php") ?>
    <?php
        if (isset($updatedsharedata)) {
            $url = "web-get-shares.php?username=" . $_GET['username'];
            echo "<meta http-equiv = 'refresh' content='2;url=" . $url . "'/>";
        }
    ?>
</head>
<body class="login">
<div class="login-header"><a href="index.php">Log Out</a>&nbsp;</div>
<table width="100%" height="95%" border="0" id="tableLayout">
    <tr>
        <td width="100%" height="100%" border="0" id="tdLayout" align="center">
        <?php
        if (isset($updatedsharedata)) {
        ?>
            <table class="tableBorder">
                <tr>
                    <td>
                        <table width="100%" height="100%" bgcolor="white" border="0" class="tableOption">
                            <tr>
                                <td colspan="3" align="center">
                                    <p>
                                        Item Deleted!<br>
                                        Redirecting to <a href="<?php echo $url ?>">Your Share Space</a>...
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        <?php
        }
        ?>
        </td>
    </tr>
</table>
</body>
</html> 