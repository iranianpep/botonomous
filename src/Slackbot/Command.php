<?php

namespace Slackbot;

class Command extends AbstractCommand
{
    /**
     * Multiple commands can refer to the same module, in other words a module can have multiple commands / actions / functions
     * If action is empty, consider 'index' as the default action
     *
     * @var array
     */
    protected static $commands = [
        'ping' => [
            'module' => 'Ping',
            'description' => 'Use as a health check'
        ],
        'pong' => [
            'module' => 'Ping',
            'action' => 'pong',
            'description' => 'Use as a health check'
        ],
        'help' => [
            'module' => 'Help',
            'description' => 'List all the available commands'
        ]
    ];
}
