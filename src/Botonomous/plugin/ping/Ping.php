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
    public function index(): string
    {
        return 'pong';
    }

    /**
     * @return string
     */
    public function pong(): string
    {
        return 'ping';
    }
}
