<?php
$config = include('config.php');
?>
<ul>
    <?php
    if (isset($config['maxsharelength'])) {
        echo "<li><i>This server allows you to share up to " . $config['maxsharelength'] . " items per share space.</i></li>";
    }
    if (isset($config['termsandconditions'])) {
        while (list($key, $val) = each($config['termsandconditions']))
        {
        echo "<li>$val</li>";
        }
    } else {
        echo "Administrator: you can define your terms and conditions by creating a config.php file -- use the config-example.php as a reference.";
    }
    if (isset($config["createkey"]) && $config["createkey"] != "" && strrpos($_SERVER['REQUEST_URI'], "web-agreement")) {
        echo "<li><i>The administrator of this service has required a create key, which will need to be provided to create a new account in a web browser. ";
        if (isset($config["admincontact"]) && $config["admincontact"] != "") {
            echo "<a href=\"javascript:document.location=atob('" . base64_encode("mailto:" . $config["admincontact"] . "?subject=Sharing Service") . "')\">Click here</a> to contact the administrator.";
        }
        echo "</i></li>";
    }
    ?>
</ul>