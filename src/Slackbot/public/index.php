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

    // Get action
    $action = '';
    if (isset($_GET['action'])) {
        $action = $_GET['action'];
    }

    switch ($action) {
        case 'oauth':
            if (isset($_GET['code'])) {
                $oAuth = new \Slackbot\OAuth();

                if ($oAuth->verifyState($_GET['state']) === true) {
                    try {
                        $accessToken = $oAuth->getAccessToken($_GET['code']);
                    } catch (Exception $e) {
                        echo $e->getMessage();
                        exit;
                    }
                } else {
                    echo 'State is not valid';
                }
            }
            break;
        default:
            /*
             * Start the engine
             */
            try {
                (new \Slackbot\Slackbot())->run();
            } catch (Exception $e) {
                echo $e->getMessage();
            }
            break;
    }
}
