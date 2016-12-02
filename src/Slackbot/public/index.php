<?php

$composerAutoload = dirname(dirname(dirname(__DIR__))) . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

require_once $composerAutoload;

/**
 * Start the engine
 */
(new \Slackbot\Slackbot($_POST))->listenToSlack();
