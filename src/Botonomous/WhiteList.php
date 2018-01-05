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
     * @throws \Exception
     *
     * @return bool
     */
    public function isWhiteListed(): bool
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
     * @throws \Exception
     *
     * @return bool
     */
    public function isUsernameWhiteListed(): bool
    {
        return empty($this->findInListByRequestKey('user_name', $this->getShortClassName(), 'username')) ? false : true;
    }

    /**
     * @throws \Exception
     *
     * @return bool
     */
    public function isUserIdWhiteListed(): bool
    {
        return empty($this->findInListByRequestKey('user_id', $this->getShortClassName(), 'userId')) ? false : true;
    }

    /**
     * @throws \Exception
     *
     * @return bool
     */
    public function isEmailWhiteListed()
    {
        return $this->checkEmail();
    }
}
