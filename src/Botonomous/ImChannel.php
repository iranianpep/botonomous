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
    public function isIm(): bool
    {
        return $this->isIm;
    }

    /**
     * @param bool $isIm
     */
    public function setIm(bool $isIm)
    {
        $this->isIm = $isIm;
    }

    /**
     * @return string
     */
    public function getUser(): string
    {
        return $this->user;
    }

    /**
     * @param string $user
     */
    public function setUser(string $user)
    {
        $this->user = $user;
    }

    /**
     * @return string
     */
    public function getCreated(): string
    {
        return $this->created;
    }

    /**
     * @param string $created
     */
    public function setCreated(string $created)
    {
        $this->created = $created;
    }

    /**
     * @return bool
     */
    public function isUserDeleted(): bool
    {
        return $this->isUserDeleted;
    }

    /**
     * @param bool $isUserDeleted
     */
    public function setUserDeleted(bool $isUserDeleted)
    {
        $this->isUserDeleted = $isUserDeleted;
    }
}
