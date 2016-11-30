<?php

require_once '../Slackbot.php';

/**
 * Start the engine
 */
(new \Slackbot\Slackbot($_POST))->listenToSlack();
