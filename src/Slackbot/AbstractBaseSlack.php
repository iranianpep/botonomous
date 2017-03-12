<?php

namespace Slackbot;

use Slackbot\utility\ClassUtility;

/**
 * Class AbstractBaseSlack
 * @package Slackbot
 *
 * General class to group all the Slack related classes e.g. AbstractSlackEntity, Event
 */
abstract class AbstractBaseSlack
{
    /**
     * @param $info
     *
     * @return mixed
     */
    public function load($info)
    {
        return (new ClassUtility())->loadAttributes($this, $info);
    }
}
