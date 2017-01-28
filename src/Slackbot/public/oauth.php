<?php

$composerAutoload = dirname(dirname(dirname(__DIR__))).DIRECTORY_SEPARATOR.'vendor'.
    DIRECTORY_SEPARATOR.'autoload.php';

$composerAutoloadExists = true;
if (!file_exists($composerAutoload)) {
    $composerAutoloadExists = false;
    echo 'Error: Could not find Composer autoload.php';
}

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
