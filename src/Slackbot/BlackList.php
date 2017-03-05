<?php

namespace Slackbot;

class BlackList extends AbstractAccessList
{
    public function __construct($request)
    {
        $this->setRequest($request);
    }

    public function isBlackListed()
    {
        if ($this->isUsernameBlackListed() !== false) {
            return true;
        }

        if ($this->isUserIdBlackListed() !== false) {
            return true;
        }

        if ($this->isEmailBlackListed() !== false) {
            return true;
        }

        return false;
    }

    public function isUsernameBlackListed()
    {
        return $this->findInListByRequestKey('user_name', 'username');
    }

    public function isUserIdBlackListed()
    {
        return $this->findInListByRequestKey('user_id', 'userId');
    }

    public function isEmailBlackListed()
    {
        // user_name is set, load the blacklist to start checking
        $list = $this->getSubAccessControlList(strtolower(__CLASS__));

        // currently if list is not set we do not check it
        if (!isset($list['userEmail'])) {
            return false;
        }

        // get user info
        $userInfo = $this->getSlackUserInfo();
        if (in_array($userInfo['profile']['email'], $list['userEmail'])) {
            return true;
        }

        return false;
    }
}
