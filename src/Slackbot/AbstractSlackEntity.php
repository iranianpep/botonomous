<?php

namespace Slackbot;

use Slackbot\utility\StringUtility;

abstract class AbstractSlackEntity
{
    protected $slackId;
    protected $name;

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

    public function load($info)
    {
        $stringUtility = new StringUtility();
        foreach ($info as $key => $value) {
            // For id, we cannot use 'set'.$stringUtility->snakeCaseToCamelCase($key) since it's named slackId
            if ($key === 'id') {
                $this->setSlackId($value);
                continue;
            }

            $method = 'set'.$stringUtility->snakeCaseToCamelCase($key);

            // ignore if setter function does not exist
            if (!method_exists($this, $method)) {
                continue;
            }

            $this->$method($value);
        }

        return $this;
    }
}
