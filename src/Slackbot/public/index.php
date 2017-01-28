<?php

$composerAutoload = dirname(dirname(dirname(__DIR__))).DIRECTORY_SEPARATOR.'vendor'.
    DIRECTORY_SEPARATOR.'autoload.php';

$composerAutoloadExists = true;
if (!file_exists($composerAutoload)) {
    $composerAutoloadExists = false;
    echo 'Error: Could not find Composer autoload.php';
}

if ($composerAutoloadExists === true) {
    /** @noinspection PhpIncludeInspection */
    require_once $composerAutoload;

    /*
     * Start the engine
     */
    try {
        (new \Slackbot\Slackbot())->run();
    } catch (Exception $e) {
        echo $e->getMessage();
    }
}
