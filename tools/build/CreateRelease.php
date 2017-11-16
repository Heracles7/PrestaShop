<?php

require_once 'Libraries/ReleaseCreator.php';
require_once 'Libraries/functions.php';
require_once 'Exception/BuildException.php';

$options = getopt('', ['version:']);

if (empty($options['version'])) {
    echo "\e[31mERROR:\n";
    echo "'version' option missing.\n";
    echo "\e[32mExample:\n";
    echo "'php CreateRelease.php --version='1.7.2.4'\e[0m\n";
    exit(1);
}

try {
    new ReleaseCreator($options['version']);
} catch (Exception $e) {
    echo "\e[31mERROR:\n";
    echo "Can not create the release.\n";
    echo "{$e->getMessage()}\e[0m\n";
    exit(1);
}
exit(0);