<?php

namespace Botonomous;

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
        return $this->isUsernameBlackListed()
            || $this->isUserIdBlackListed()
            || $this->isEmailBlackListed();
    }

    /**
     * @return bool
     */
    public function isUsernameBlackListed()
    {
        return $this->findInListByRequestKey('user_name', $this->getShortClassName(), 'username') === true
            ? true : false;
    }

    /**
     * @return bool
     */
    public function isUserIdBlackListed()
    {
        return $this->findInListByRequestKey('user_id', $this->getShortClassName(), 'userId') === true
            ? true : false;
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

        return $this->isEmailInList($list);
    }
}
