<?php

namespace Slackbot\plugin;

/**
 * Class Ping
 * @package Slackbot\plugin
 */
class Ping extends AbstractPlugin
{
    /**
     * @return string
     */
    public function index()
    {
        return 'pong';
    }

    /**
     * @return string
     */
    public function pong()
    {
        return 'ping';
    }
}
