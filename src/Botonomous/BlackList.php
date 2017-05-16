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
        if ($this->isUsernameBlackListed() !== false) {
            return true;
        }

        if ($this->isUserIdBlackListed() !== false) {
            return true;
        }

        return $this->isEmailBlackListed() !== false ? true : false;
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
        return $this->checkEmail();
    }
}
