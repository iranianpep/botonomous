<?php

namespace Slackbot;

class MessageAction extends AbstractBaseSlack
{
    private $actions;
    private $callbackId;
    private $team;
    private $channel;
    private $user;
    private $actionTimestamp;
    private $messageTimestamp;
    private $attachmentId;
    private $token;
    private $originalMessage;
    private $responseUrl;

    /**
     * @return array
     */
    public function getActions()
    {
        return $this->actions;
    }

    /**
     * @param array $actions
     */
    public function setActions(array $actions)
    {
        $this->actions = $actions;
    }

    /**
     * @param Action $action
     */
    public function addAction(Action $action)
    {
        $actions = $this->getActions();
        $actions[] = $action;
        $this->setActions($actions);
    }

    /**
     * @return string
     */
    public function getCallbackId()
    {
        return $this->callbackId;
    }

    /**
     * @param string $callbackId
     */
    public function setCallbackId($callbackId)
    {
        $this->callbackId = $callbackId;
    }

    /**
     * @return Team
     */
    public function getTeam()
    {
        return $this->team;
    }

    /**
     * @param mixed $team
     */
    public function setTeam($team)
    {
        if ($team instanceof Team) {
            $this->team = $team;
            return;
        }

        // if array or json is passed create the object
        $this->team = (new Team())->load($team);
    }

    /**
     * @return Channel
     */
    public function getChannel()
    {
        return $this->channel;
    }

    /**
     * @param mixed $channel
     */
    public function setChannel($channel)
    {
        if ($channel instanceof Channel) {
            $this->channel = $channel;
            return;
        }

        // if array or json is passed create the object
        $this->channel = (new Channel())->load($channel);
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param mixed $user
     */
    public function setUser($user)
    {
        if ($user instanceof User) {
            $this->user = $user;
            return;
        }

        // if array or json is passed create the object
        $this->user = (new User())->load($user);
    }

    /**
     * @return string
     */
    public function getActionTimestamp()
    {
        return $this->actionTimestamp;
    }

    /**
     * @param string $actionTimestamp
     */
    public function setActionTimestamp($actionTimestamp)
    {
        $this->actionTimestamp = $actionTimestamp;
    }

    /**
     * @return string
     */
    public function getMessageTimestamp()
    {
        return $this->messageTimestamp;
    }

    /**
     * @param string $messageTimestamp
     */
    public function setMessageTimestamp($messageTimestamp)
    {
        $this->messageTimestamp = $messageTimestamp;
    }

    /**
     * @return string
     */
    public function getAttachmentId()
    {
        return $this->attachmentId;
    }

    /**
     * @param string $attachmentId
     */
    public function setAttachmentId($attachmentId)
    {
        $this->attachmentId = $attachmentId;
    }

    /**
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * @param string $token
     */
    public function setToken($token)
    {
        $this->token = $token;
    }

    /**
     * @return string
     */
    public function getOriginalMessage()
    {
        return $this->originalMessage;
    }

    /**
     * @param string $originalMessage
     */
    public function setOriginalMessage($originalMessage)
    {
        $this->originalMessage = $originalMessage;
    }

    /**
     * @return string
     */
    public function getResponseUrl()
    {
        return $this->responseUrl;
    }

    /**
     * @param string $responseUrl
     */
    public function setResponseUrl($responseUrl)
    {
        $this->responseUrl = $responseUrl;
    }

    public function load($info)
    {
        $thisObject = parent::load($info);

        $actions = [];
        foreach ($info['actions'] as $actionInfo) {
            // load action
            $actions[] = (new Action())->load($actionInfo);
        }
        $this->setActions($actions);
        /*
         * Finish adding actions
         */

        return $thisObject;
    }
}
