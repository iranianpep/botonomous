<?php

namespace Botonomous\plugin;

use Botonomous\Dictionary;
use Botonomous\Slackbot;

/**
 * Class AbstractPlugin.
 */
abstract class AbstractPlugin implements PluginInterface
{
    protected $slackbot;

    /**
     * Dependencies.
     */
    protected $dictionary;

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
     * Return Botonomous.
     *
     * @return Slackbot
     */
    public function getSlackbot()
    {
        return $this->slackbot;
    }

    /**
     * Set Botonomous.
     *
     * @param Slackbot $slackbot
     */
    public function setSlackbot($slackbot)
    {
        $this->slackbot = $slackbot;
    }

    /**
     * @return Dictionary
     */
    public function getDictionary()
    {
        if (!isset($this->dictionary)) {
            $this->setDictionary((new Dictionary()));
        }

        return $this->dictionary;
    }

    /**
     * @param Dictionary $dictionary
     */
    public function setDictionary(Dictionary $dictionary)
    {
        $this->dictionary = $dictionary;
    }
}
