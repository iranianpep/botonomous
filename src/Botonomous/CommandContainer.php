<?php

namespace Botonomous;

/**
 * Class CommandContainer.
 */
class CommandContainer extends AbstractCommandContainer
{
    const HEALTH_CHECK_PLUGIN_DESCRIPTION = 'Use as a health check';
    const PLUGIN_KEY = 'plugin';

    /**
     * Multiple commands can refer to the same plugin,
     * in other words a plugin can have multiple commands / actions / functions
     * If action is empty, consider 'index' as the default action.
     *
     * @var array
     */
    protected static $commands = [
        'ping' => [
            self::PLUGIN_KEY      => 'Ping',
            'description'         => self::HEALTH_CHECK_PLUGIN_DESCRIPTION,
        ],
        'pong' => [
            self::PLUGIN_KEY      => 'Ping',
            'action'              => 'pong',
            'description'         => self::HEALTH_CHECK_PLUGIN_DESCRIPTION,
        ],
        'commandWithoutFunctionForTest' => [
            self::PLUGIN_KEY      => 'Ping',
            'action'              => 'commandWithoutFunctionForTest',
            'description'         => self::HEALTH_CHECK_PLUGIN_DESCRIPTION,
        ],
        'help' => [
            self::PLUGIN_KEY      => 'Help',
            'description'         => 'List all the available commands',
            'keywords'            => [
                'help',
                'ask',
            ],
        ],
        'qa' => [
            self::PLUGIN_KEY      => 'QA',
            'description'         => 'Answer questions',
        ],
    ];
}
