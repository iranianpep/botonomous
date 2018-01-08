<?php

namespace Botonomous;

/**
 * Class CommandContainer.
 */
class CommandContainer extends AbstractCommandContainer
{
    const HEALTH_CHECK_PLUGIN_DESCRIPTION = 'Use as a health check';
    const PLUGIN_KEY = 'plugin';
    const ACTION_KEY = 'action';
    const DESCRIPTION_KEY = 'description';
    const KEYWORDS_KEY = 'keywords';

    /**
     * Multiple commands can refer to the same plugin,
     * in other words a plugin can have multiple commands / actions / functions
     * If action is empty, consider 'index' as the default action.
     *
     * @var array
     */
    protected static $commands = [
        'ping' => [
            self::PLUGIN_KEY              => 'Ping',
            self::DESCRIPTION_KEY         => self::HEALTH_CHECK_PLUGIN_DESCRIPTION,
        ],
        'pong' => [
            self::PLUGIN_KEY               => 'Ping',
            self::ACTION_KEY               => 'pong',
            self::DESCRIPTION_KEY          => self::HEALTH_CHECK_PLUGIN_DESCRIPTION,
        ],
        'commandWithoutFunctionForTest' => [
            self::PLUGIN_KEY               => 'Ping',
            self::ACTION_KEY               => 'commandWithoutFunctionForTest',
            self::DESCRIPTION_KEY          => self::HEALTH_CHECK_PLUGIN_DESCRIPTION,
        ],
        'help' => [
            self::PLUGIN_KEY               => 'Help',
            self::DESCRIPTION_KEY          => 'List all the available commands',
            self::KEYWORDS_KEY             => [
                'help',
                'ask',
            ],
        ],
        'qa' => [
            self::PLUGIN_KEY               => 'QA',
            self::DESCRIPTION_KEY          => 'Answer questions',
        ],
    ];
}
