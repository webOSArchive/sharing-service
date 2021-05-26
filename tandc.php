<?php
$config = include('config.php');
?>
<ul>
    <li>This server allows you to share up to <?php echo $config['maxsharelength'] ?> items per share space.</li>
    <li>There is no guarantee of privacy or performance. This is a shared server, designed for retro devices. User content is not encrypted, and the service may go down without notice.</li>
    <li>This server does not allow the sharing of pornography, legal or otherwise. The host of this service will comply with any legal requests for logs or data. Your IP address will likely be visible in the logs, and the host provides no protection or indemnification.</li>
    <li>The host of this service reserves the right to deny service to anyone whose use of the service is not in good faith or has the appearance of being unreasonable, damaging, or irresponsible.</li>
    <li>If your share space goes unused for more than 30 days, it may be automatically purged.</li>
    <li>Lost passwords cannot be recovered or reset. Please record your credentials in a secure location.</li>
    <?php
    if (isset($config["createkey"]) && $config["createkey"] != "") {
    ?>
        <li><i>The administrator of this service has required a create key, which will need to be provided to create a new account.</i></li>
    <?php
    }
    ?>
</ul>