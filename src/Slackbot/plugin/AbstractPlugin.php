<?php

namespace Slackbot\plugin;

use Slackbot\Slackbot;

abstract class AbstractPlugin implements PluginInterface
{
    protected $slackbot;

    public function __construct(Slackbot $slackbot)
    {
        $this->setSlackbot($slackbot);
    }

    /**
     * Return Slackbot
     *
     * @return Slackbot
     */
    public function getSlackbot()
    {
        return $this->slackbot;
    }

    /**
     * Set Slackbot
     *
     * @param Slackbot $slackbot
     */
    public function setSlackbot($slackbot)
    {
        $this->slackbot = $slackbot;
    }
}
