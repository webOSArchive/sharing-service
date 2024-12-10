# sharing-service

A simple, retro-device friendly sharing service

Don't @ me cause I'm using tables for layout. This web app was designed to work with old and new browsers.

## System Requirements

- requires php-curl
- run `composer require maestroerror/php-heic-to-jpg` in the root of this folder
- install heir-converter-image per its docs: https://github.com/MaestroError/heif-converter-image

web server user will need read/write access to the `data` folder and all contents

## Setup

copy `config-example.php` to `config.php` and set the values as desired.

## Troubleshooting

Problems occur with certain HEIC/HEIF images that masquarade as JPGS. This is handled by the libraries mentioned above, but installation may be different for your environment. The critical conversion happens in `ithumb.php` when PHP is unable to load info from the image.

This is Samsung's fault -- not mine!