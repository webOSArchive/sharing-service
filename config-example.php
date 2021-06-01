<?php
return array(
	'clientids' => array (
        'test',     //used to validate clients, kind of like an api key, use only numbers and lowercase letters
    ),
    'createkey' => 'creator',   //use a key if you want to limit new accounts, use only numbers and lowercase letters
    'readonlykey' => "readonlytest",    //used internally to load data, should be unique and not guessable, use only numbers and lowercase letters
    'shorturl' => 'http://short.link/', //if your webserver has a shorter, alternate domain name, enter it here, otherwise leave empty
    'maxsharelength' => 20,     //how many shares can each user have without rolling over
    'maximagesize' => 3072000,  //in bytes
    'maxtextlength' => 5000,    //in bytes
    'admincontact' => 'your@email',  //will be encoded in the agreement webpage if set
    'allowedhtml' => '<p><b><i><u><br><ul><li><font>',   //HTML could mess up the client, or be used as an attack, allow only certain safer tags
    'allowhttps' => false,   //if your server supports https, set to true to advertise the option. requiring https is up to your server (redirect) config.
    'termsandconditions' => array (    //you can customize the terms of service here
        'There is no guarantee of privacy or performance. This is a shared server, designed for retro devices. User content is not encrypted, and the service may go down without notice.',
        'This server does not allow the sharing of pornography, legal or otherwise. The host of this service will comply with any legal requests for logs or data. Your IP address will likely be visible in the logs, and the host provides no protection or indemnification.',
        'The host of this service reserves the right to deny service to anyone whose use of the service is not in good faith or has the appearance of being unreasonable, damaging, or irresponsible.',
        'If your share space goes unused for more than 30 days, it may be automatically purged.',
        'Lost passwords cannot be recovered or reset. Please record your credentials in a secure location.',
    )
);
?>