<?php

namespace Botonomous;

/**
 * Class CommandContainer.
 */
class CommandContainer extends AbstractCommandContainer
{
    const HEALTH_CHECK_PLUGIN_DESCRIPTION = 'Use as a health check';

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
            'description' => self::HEALTH_CHECK_PLUGIN_DESCRIPTION,
        ],
        'pong' => [
            'plugin'      => 'Ping',
            'action'      => 'pong',
            'description' => self::HEALTH_CHECK_PLUGIN_DESCRIPTION,
        ],
        'commandWithoutFunctionForTest' => [
            'plugin'      => 'Ping',
            'action'      => 'commandWithoutFunctionForTest',
            'description' => self::HEALTH_CHECK_PLUGIN_DESCRIPTION,
        ],
        'help' => [
            'plugin'      => 'Help',
            'description' => 'List all the available commands',
            'keywords'    => [
                'help',
                'ask',
            ],
        ],
        'qa' => [
            'plugin'      => 'QA',
            'description' => 'Answer questions',
        ],
    ];
}
