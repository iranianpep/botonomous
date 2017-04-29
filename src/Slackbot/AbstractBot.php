<?php

namespace Slackbot;

use Slackbot\listener\AbstractBaseListener;
use Slackbot\utility\FormattingUtility;
use Slackbot\utility\LoggerUtility;
use Slackbot\utility\MessageUtility;
use Slackbot\utility\RequestUtility;

abstract class AbstractBot
{
    /**
     * Dependencies.
     */
    protected $config;
    protected $listener;
    protected $messageUtility;
    protected $commandContainer;
    protected $formattingUtility;
    protected $loggerUtility;
    protected $oauth;
    protected $requestUtility;
    protected $blackList;
    protected $whiteList;
    protected $sender;

    /**
     * @return Config
     */
    public function getConfig()
    {
        if ($this->config === null) {
            $this->config = (new Config());
        }

        return $this->config;
    }

    /**
     * @param Config $config
     */
    public function setConfig(Config $config)
    {
        $this->config = $config;
    }

    /**
     * @return AbstractBaseListener
     */
    public function getListener()
    {
        if (!isset($this->listener)) {
            $listenerClass = __NAMESPACE__.'\\listener\\'.ucwords($this->getConfig()->get('listenerType')).'Listener';
            $this->setListener(new $listenerClass());
        }

        return $this->listener;
    }

    public function setListener(AbstractBaseListener $listener)
    {
        $this->listener = $listener;
    }

    /**
     * @return MessageUtility
     */
    public function getMessageUtility()
    {
        if (!isset($this->messageUtility)) {
            $this->setMessageUtility(new MessageUtility());
        }

        return $this->messageUtility;
    }

    /**
     * @param MessageUtility $messageUtility
     */
    public function setMessageUtility(MessageUtility $messageUtility)
    {
        $this->messageUtility = $messageUtility;
    }

    /**
     * @return CommandContainer
     */
    public function getCommandContainer()
    {
        if (!isset($this->commandContainer)) {
            $this->setCommandContainer(new CommandContainer());
        }

        return $this->commandContainer;
    }

    /**
     * @param CommandContainer $commandContainer
     */
    public function setCommandContainer(CommandContainer $commandContainer)
    {
        $this->commandContainer = $commandContainer;
    }

    /**
     * @return FormattingUtility
     */
    public function getFormattingUtility()
    {
        if (!isset($this->formattingUtility)) {
            $this->setFormattingUtility(new FormattingUtility());
        }

        return $this->formattingUtility;
    }

    /**
     * @param FormattingUtility $formattingUtility
     */
    public function setFormattingUtility(FormattingUtility $formattingUtility)
    {
        $this->formattingUtility = $formattingUtility;
    }

    /**
     * @return LoggerUtility
     */
    public function getLoggerUtility()
    {
        if (!isset($this->loggerUtility)) {
            $this->setLoggerUtility(new LoggerUtility());
        }

        return $this->loggerUtility;
    }

    /**
     * @param LoggerUtility $loggerUtility
     */
    public function setLoggerUtility(LoggerUtility $loggerUtility)
    {
        $this->loggerUtility = $loggerUtility;
    }

    /**
     * @return OAuth
     */
    public function getOauth()
    {
        if (!isset($this->oauth)) {
            $this->setOauth(new OAuth());
        }

        return $this->oauth;
    }

    /**
     * @param OAuth $oauth
     */
    public function setOauth(OAuth $oauth)
    {
        $this->oauth = $oauth;
    }

    /**
     * @return RequestUtility
     */
    public function getRequestUtility()
    {
        if (!isset($this->requestUtility)) {
            $this->setRequestUtility((new RequestUtility()));
        }

        return $this->requestUtility;
    }

    /**
     * @param RequestUtility $requestUtility
     */
    public function setRequestUtility(RequestUtility $requestUtility)
    {
        $this->requestUtility = $requestUtility;
    }

    /**
     * @return BlackList
     */
    public function getBlackList()
    {
        if (!isset($this->blackList)) {
            $this->setBlackList(new BlackList($this->getListener()->getRequest()));
        }

        return $this->blackList;
    }

    /**
     * @param BlackList $blackList
     */
    public function setBlackList(BlackList $blackList)
    {
        $this->blackList = $blackList;
    }

    /**
     * @return WhiteList
     */
    public function getWhiteList()
    {
        if (!isset($this->whiteList)) {
            $this->setWhiteList(new WhiteList($this->getListener()->getRequest()));
        }

        return $this->whiteList;
    }

    /**
     * @param WhiteList $whiteList
     */
    public function setWhiteList(WhiteList $whiteList)
    {
        $this->whiteList = $whiteList;
    }

    /**
     * @return Sender
     */
    public function getSender()
    {
        if (!isset($this->sender)) {
            $this->setSender(new Sender($this));
        }

        return $this->sender;
    }

    /**
     * @param Sender $sender
     */
    public function setSender(Sender $sender)
    {
        $this->sender = $sender;
    }
}
