<?php

namespace Botonomous;

class WhiteList extends AbstractAccessList
{
    /**
     * WhiteList constructor.
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

        return $usernameCheck === true && $userIdCheck === true && $userEmailCheck === true ? true : false;
    }

    /**
     * @return bool
     */
    public function isUsernameWhiteListed()
    {
        return empty($this->findInListByRequestKey('user_name', $this->getShortClassName(), 'username')) ? false : true;
    }

    /**
     * @return bool
     */
    public function isUserIdWhiteListed()
    {
        return empty($this->findInListByRequestKey('user_id', $this->getShortClassName(), 'userId')) ? false : true;
    }

    /**
     * @return bool
     * @return mixed
     */
    public function isEmailWhiteListed()
    {
        // user_name is set, load the blacklist to start checking
        $list = $this->getSubAccessControlList($this->getShortClassName());

        // currently if list is not set we do not check it
        if (!isset($list['userEmail'])) {
            return true;
        }

        return $this->isEmailInList($list);
    }
}
