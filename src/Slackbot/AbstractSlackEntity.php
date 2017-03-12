<?php

namespace Slackbot;

/**
 * Class AbstractSlackEntity
 * @package Slackbot
 *
 * More specific class comparing to AbstractBaseSlack to group classes e.g. Team, Channel, User, ...
 */
abstract class AbstractSlackEntity extends AbstractBaseSlack
{
    protected $slackId;
    protected $name;

    /**
     * @return string
     */
    public function getSlackId()
    {
        return $this->slackId;
    }

    /**
     * @param string $slackId
     */
    public function setSlackId($slackId)
    {
        $this->slackId = $slackId;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }
}
