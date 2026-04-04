<?php
return array(

    // API key(s) that clients must send in the client-id header.
    // For a private server, one short random value is sufficient.
    // Use only lowercase letters and numbers.
    'clientids' => array(
        'changeme',
    ),

    // Key required to create new accounts. Leave empty to allow anyone to register.
    // For a private household server, set this to something only you know.
    // Use only lowercase letters and numbers.
    'createkey' => '',

    // Used internally for read-only data access. Should be unique and not guessable.
    // Use only lowercase letters and numbers.
    'readonlykey' => 'readonlychangeme',

    // If your server has a shorter alternate domain, enter it here.
    // Leave empty to use the server's own hostname for share links.
    'shorturl' => '',

    // Maximum number of share items per user before oldest items roll off.
    'maxsharelength' => 20,

    // Maximum uploaded image size in bytes (default ~3MB).
    'maximagesize' => 3072000,

    // Maximum text share length in bytes.
    'maxtextlength' => 5000,

    // Your contact email, shown to users if a create key is required.
    // Leave empty to hide contact info.
    'admincontact' => '',

    // HTML tags allowed in text shares. Keep this restrictive.
    'allowedhtml' => '<p><b><i><u><br><ul><li><font>',

    // Set to true if your server is accessible over HTTPS.
    // This advertises the HTTPS option to web users; enforcing HTTPS is handled
    // by your server or reverse proxy configuration.
    'allowhttps' => true,

    // Name shown on the web landing page for self-hosted instances.
    'site_name' => 'Your Share Space',

    // Welcome message shown on the web landing page for self-hosted instances.
    'welcome_message' => 'Welcome to your personal Share Space. Use the Share Space app on your webOS device to connect.',

    // Terms shown to users when creating an account.
    // Customize these to reflect your own policies.
    'termsandconditions' => array(
        'This is a self-hosted service. The operator of this server is responsible for its use and content.',
        'There is no guarantee of privacy or performance. User content is not encrypted in storage.',
        'Lost passwords cannot be recovered or reset. Please record your credentials in a secure location.',
    )
);
?>
