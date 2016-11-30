<?php

namespace Slackbot;

class Command
{
    /**
     * Multiple commands can refer to the same module
     * If action is not set, the name of the command is considered as the action
     *
     * @var array
     */
    public static $commands = [
        'help' => [
            'module' => 'help'
        ]
    ];
}
