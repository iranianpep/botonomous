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
     * @throws \Exception
     *
     * @return bool
     */
    public function isBlackListed(): bool
    {
        return $this->isUsernameBlackListed()
            || $this->isUserIdBlackListed()
            || $this->isEmailBlackListed();
    }

    /**
     * @throws \Exception
     *
     * @return bool
     */
    public function isUsernameBlackListed(): bool
    {
        return $this->findInListByRequestKey('user_name', $this->getShortClassName(), 'username') === true
            ? true : false;
    }

    /**
     * @throws \Exception
     *
     * @return bool
     */
    public function isUserIdBlackListed(): bool
    {
        return $this->findInListByRequestKey('user_id', $this->getShortClassName(), 'userId') === true
            ? true : false;
    }

    /**
     * @throws \Exception
     *
     * @return bool
     */
    public function isEmailBlackListed()
    {
        return $this->checkEmail();
    }
}
