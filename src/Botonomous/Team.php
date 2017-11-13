<?php

namespace Botonomous;

/**
 * Class Team.
 */
class Team extends AbstractSlackEntity
{
    private $name;
    private $domain;
    private $emailDomain;
    private $icon;

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getDomain(): string
    {
        return $this->domain;
    }

    /**
     * @param string $domain
     */
    public function setDomain(string $domain)
    {
        $this->domain = $domain;
    }

    /**
     * @return string
     */
    public function getEmailDomain(): string
    {
        return $this->emailDomain;
    }

    /**
     * @param string $emailDomain
     */
    public function setEmailDomain(string $emailDomain)
    {
        $this->emailDomain = $emailDomain;
    }

    /**
     * @return array
     */
    public function getIcon(): array
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
    public function isIconDefault(): bool
    {
        $icon = $this->getIcon();

        return isset($icon['image_default']) && $icon['image_default'] === true ? true : false;
    }
}
