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
     * @throws \Exception
     */
    public function isBlackListed(): bool
    {
        return $this->isUsernameBlackListed()
            || $this->isUserIdBlackListed()
            || $this->isEmailBlackListed();
    }

    /**
     * @return bool
     * @throws \Exception
     */
    public function isUsernameBlackListed(): bool
    {
        return $this->findInListByRequestKey('user_name', $this->getShortClassName(), 'username') === true
            ? true : false;
    }

    /**
     * @return bool
     * @throws \Exception
     */
    public function isUserIdBlackListed(): bool
    {
        return $this->findInListByRequestKey('user_id', $this->getShortClassName(), 'userId') === true
            ? true : false;
    }

    /**
     * @return bool
     * @throws \Exception
     */
    public function isEmailBlackListed()
    {
        return $this->checkEmail();
    }
}
