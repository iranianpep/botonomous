<?php

namespace Slackbot\plugin\ping;

use Slackbot\plugin\AbstractPlugin;

/**
 * Class Ping.
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
