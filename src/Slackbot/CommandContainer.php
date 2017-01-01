<?php

namespace Slackbot;

/**
 * Class CommandContainer.
 */
class CommandContainer extends AbstractCommandContainer
{
    /**
     * Multiple commands can refer to the same plugin,
     * in other words a plugin can have multiple commands / actions / functions
     * If action is empty, consider 'index' as the default action.
     *
     * @var array
     */
    protected static $commands = [
        'ping' => [
            'plugin'      => 'Ping',
            'description' => 'Use as a health check',
        ],
        'pong' => [
            'plugin'      => 'Ping',
            'action'      => 'pong',
            'description' => 'Use as a health check',
        ],
        'commandWithoutFunctionForTest' => [
            'plugin'      => 'Ping',
            'action'      => 'commandWithoutFunctionForTest',
            'description' => 'Use as a health check',
        ],
        'help' => [
            'plugin'      => 'Help',
            'description' => 'List all the available commands',
        ],
    ];
}
