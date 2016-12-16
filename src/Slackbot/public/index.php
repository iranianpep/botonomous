<?php

$composerAutoload = dirname(dirname(dirname(__DIR__))).DIRECTORY_SEPARATOR.'vendor'.
    DIRECTORY_SEPARATOR.'autoload.php';

if (!file_exists($composerAutoload)) {
    echo 'Error: Could not find Composer autoload.php';
    exit;
}

require_once $composerAutoload;

/*
 * Start the engine
 */
try {
    (new \Slackbot\Slackbot($_POST))->listenToSlack();
} catch (Exception $e) {
    echo $e->getMessage();
}
