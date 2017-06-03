<?php

namespace Botonomous;

/**
 * Class ImChannel.
 *
 * Model for ImChannels
 */
class ImChannel extends AbstractSlackEntity
{
    private $isIm;
    private $user;
    private $created;
    private $isUserDeleted;

    /**
     * @return bool
     */
    public function isIm()
    {
        return $this->isIm;
    }

    /**
     * @param bool $isIm
     */
    public function setIm($isIm)
    {
        $this->isIm = $isIm;
    }

    /**
     * @return string
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param string $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }

    /**
     * @return string
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * @param string $created
     */
    public function setCreated($created)
    {
        $this->created = $created;
    }

    /**
     * @return bool
     */
    public function isUserDeleted()
    {
        return $this->isUserDeleted;
    }

    /**
     * @param bool $isUserDeleted
     */
    public function setUserDeleted($isUserDeleted)
    {
        $this->isUserDeleted = $isUserDeleted;
    }
}
