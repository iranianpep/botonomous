<?php

namespace Slackbot;

class WhiteList extends AbstractAccessList
{
    public function __construct($request)
    {
        $this->setRequest($request);
    }

    public function isWhiteListed()
    {
        $usernameCheck = true;
        $userIdCheck = true;
        $userEmailCheck = true;

        if ($this->isUsernameWhiteListed() === false) {
            $usernameCheck = false;
        }

        if ($this->isUserIdWhiteListed() === false) {
            $userIdCheck = false;
        }

        if ($this->isEmailWhiteListed() === false) {
            $userEmailCheck = false;
        }

        if ($usernameCheck === true && $userIdCheck === true && $userEmailCheck === true) {
            return true;
        }

        return false;
    }

    public function isUsernameWhiteListed()
    {
        return $this->findInListByRequestKey('user_name', $this->getShortClassName(), 'username');
    }

    public function isUserIdWhiteListed()
    {
        return $this->findInListByRequestKey('user_id', $this->getShortClassName(), 'userId');
    }

    public function isEmailWhiteListed()
    {
        // user_name is set, load the blacklist to start checking
        $list = $this->getSubAccessControlList($this->getShortClassName());

        // currently if list is not set we do not check it
        if (!isset($list['userEmail'])) {
            return true;
        }

        // get user info
        $userInfo = $this->getSlackUserInfo();
        if (in_array($userInfo['profile']['email'], $list['userEmail'])) {
            return true;
        }

        return false;
    }
}
