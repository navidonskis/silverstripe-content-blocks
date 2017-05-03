<?php

define('CONTENT_BLOCKS_DIR', basename(dirname(__FILE__)));

Config::inst()->update('LeftAndMain', 'extra_requirements_javascript', [
    CONTENT_BLOCKS_DIR."/assets/javascript/maps-backend.js",
]);

Config::inst()->update('LeftAndMain', 'extra_requirements_css', [
    CONTENT_BLOCKS_DIR.'/assets/styles/app.css',
]);