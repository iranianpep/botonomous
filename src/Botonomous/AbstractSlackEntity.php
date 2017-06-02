<?php

namespace Botonomous;

/**
 * Class AbstractSlackEntity.
 */
abstract class AbstractSlackEntity extends AbstractBaseSlack
{
    protected $slackId;

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
}
