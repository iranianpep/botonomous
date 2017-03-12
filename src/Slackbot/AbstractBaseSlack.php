<?php

namespace Slackbot;

use Slackbot\utility\ClassUtility;

/**
 * Class AbstractBaseSlack.
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
