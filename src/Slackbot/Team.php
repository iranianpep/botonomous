<?php

namespace Slackbot;

/**
 * Class Team.
 */
class Team
{
    private $slackId;
    private $name;
    private $domain;
    private $emailDomain;
    private $icon;

    /**
     * @return string
     */
    public function getSlackId()
    {
        return $this->slackId;
    }

    /**
     * @param string $slackId
     */
    public function setSlackId($slackId)
    {
        $this->slackId = $slackId;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

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

        if (isset($icon['image_default']) && $icon['image_default'] === true) {
            return true;
        }

        return false;
    }
}
