<?php

namespace Slackbot;

abstract class AbstractCommand
{
    protected static $commands;

    public function get($key)
    {
        if (array_key_exists($key, static::$commands)) {
            $commandDetails = static::$commands[$key];

            // If action is empty, consider 'index' as the default action
            if (empty($commandDetails['action'])) {
                $commandDetails['action'] = 'index';
            }

            return $commandDetails;
        } else {
            return null;
        }
    }
}
