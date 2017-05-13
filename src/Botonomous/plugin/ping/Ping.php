<?php

namespace Botonomous\plugin\ping;

use Botonomous\plugin\AbstractPlugin;

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
