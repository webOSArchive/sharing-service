<?php
return array(
	'clientids' => array (
        'test',     //used to validate clients, kind of like an api key
    ),
    'createkey' => 'creator',   //use a key if you want to limit new accounts
    'readonlykey' => "readonlytest",    //used internally to load data, should be unique and not guessable
    'shorturl' => 'http://short.link/', //if your webserver has a shorter, alternate domain name, enter it here
    'maxsharelength' => 20,     //how many shares can each user have without rolling over
    'maximagesize' => 3072000,  //in bytes
    'maxtextlength' => 5000,    //in bytes
    'admincontact' => 'your@email',  //will be encoded in the agreement webpage
    'allowedhtml' => '<p><b><i><u><br><ul><li><font>'   //HTML could mess up the client, or be used as an attack, allow only certain safer tags
);
?>