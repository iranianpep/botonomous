<?php

$composerAutoload = dirname(dirname(dirname(__DIR__))) . DIRECTORY_SEPARATOR . 'vendor' .
    DIRECTORY_SEPARATOR . 'autoload.php';

if (!file_exists($composerAutoload)) {
    echo 'Error: Could not find Composer autoload.php';
    exit;
}

require_once $composerAutoload;

/**
 * Start the engine
 */
(new \Slackbot\Slackbot($_POST))->listenToSlack();
