<?php

namespace Slackbot\plugin;

use Slackbot\Slackbot;

/**
 * Class AbstractPlugin.
 */
abstract class AbstractPlugin implements PluginInterface
{
    protected $slackbot;

    /**
     * AbstractPlugin constructor.
     *
     * @param Slackbot $slackbot
     */
    public function __construct(Slackbot $slackbot)
    {
        $this->setSlackbot($slackbot);
    }

    /**
     * Return Slackbot.
     *
     * @return Slackbot
     */
    public function getSlackbot()
    {
        return $this->slackbot;
    }

    /**
     * Set Slackbot.
     *
     * @param Slackbot $slackbot
     */
    public function setSlackbot($slackbot)
    {
        $this->slackbot = $slackbot;
    }
}
