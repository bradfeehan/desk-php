<?php

error_reporting(-1);

$ds = DIRECTORY_SEPARATOR;

$autoloader = dirname(__DIR__) . "{$ds}vendor{$ds}autoload.php";

// Ensure that Composer dependencies have been installed locally
if (!file_exists($autoloader)) {
    die(
        "Dependencies must be installed using Composer:\n\n" .
        "composer.phar install --dev\n\n" .
        "See https://github.com/composer/composer/blob/master/README.md " .
        "for help with installing composer\n"
    );
}

// Include the Composer autoloader
require_once $autoloader;
