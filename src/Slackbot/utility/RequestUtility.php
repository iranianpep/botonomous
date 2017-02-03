<?php

namespace Slackbot\utility;

/**
 * Class RequestUtility
 * @package Slackbot\utility
 */
class RequestUtility
{
    /**
     * @return string
     */
    public function getContent()
    {
        return file_get_contents('php://input');
    }

    /**
     * @return mixed
     */
    public function getPost()
    {
        return filter_input_array(INPUT_POST);
    }

    /**
     * @return mixed
     */
    public function getGet()
    {
        return filter_input_array(INPUT_GET);
    }

    /**
     * @return mixed
     */
    public function getServerProtocol()
    {
        return filter_input(INPUT_SERVER, 'SERVER_PROTOCOL', FILTER_SANITIZE_STRING);
    }
}
