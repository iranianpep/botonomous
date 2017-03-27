<?php

namespace Slackbot;

class BlackList extends AbstractAccessList
{
    /**
     * BlackList constructor.
     *
     * @param $request
     */
    public function __construct($request)
    {
        $this->setRequest($request);
    }

    /**
     * @return bool
     */
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

    /**
     * @return bool
     */
    public function isUsernameBlackListed()
    {
        if ($this->findInListByRequestKey('user_name', $this->getShortClassName(), 'username') === true) {
            return true;
        }

        return false;
    }

    /**
     * @return bool
     */
    public function isUserIdBlackListed()
    {
        if ($this->findInListByRequestKey('user_id', $this->getShortClassName(), 'userId') === true) {
            return true;
        }

        return false;
    }

    /**
     * @return bool
     * @return mixed
     */
    public function isEmailBlackListed()
    {
        // user_name is set, load the blacklist to start checking
        $list = $this->getSubAccessControlList($this->getShortClassName());

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
