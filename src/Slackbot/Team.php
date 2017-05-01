<?php

namespace Slackbot;

/**
 * Class Team.
 */
class Team extends AbstractSlackEntity
{
    private $domain;
    private $emailDomain;
    private $icon;

    /**
     * @return string
     */
    public function getDomain()
    {
        return $this->domain;
    }

    /**
     * @param string $domain
     */
    public function setDomain($domain)
    {
        $this->domain = $domain;
    }

    /**
     * @return string
     */
    public function getEmailDomain()
    {
        return $this->emailDomain;
    }

    /**
     * @param string $emailDomain
     */
    public function setEmailDomain($emailDomain)
    {
        $this->emailDomain = $emailDomain;
    }

    /**
     * @return array
     */
    public function getIcon()
    {
        return $this->icon;
    }

    /**
     * @param array $icon
     */
    public function setIcon(array $icon)
    {
        $this->icon = $icon;
    }

    /**
     * @return bool
     */
    public function isIconDefault()
    {
        $icon = $this->getIcon();

        return isset($icon['image_default']) && $icon['image_default'] === true ? true : false;
    }
}
