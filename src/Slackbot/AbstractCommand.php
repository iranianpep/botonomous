<?php

namespace Slackbot;

abstract class AbstractCommand
{
    protected static $commands;

    public function get($key)
    {
        $commands = $this->getAll();

        if (!array_key_exists($key, $commands)) {
            return null;
        }

        return $commands[$key];
    }

    public function getAll()
    {
        $commands = static::$commands;

        if (!empty($commands)) {
            foreach ($commands as $commandName => $commandDetails) {
                // If action is empty, consider 'index' as the default action
                if (empty($commandDetails['action'])) {
                    $commands[$commandName]['action'] = 'index';
                }

                // populate the class
                $module = $commandDetails['module'];
                $commands[$commandName]['class'] = __NAMESPACE__ . "\\plugin\\{$module}";
            }
        }

        return $commands;
    }
}
