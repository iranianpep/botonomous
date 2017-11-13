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
    public function getSlackId(): string
    {
        return $this->slackId;
    }

    /**
     * @param string $slackId
     */
    public function setSlackId(string $slackId)
    {
        $this->slackId = $slackId;
    }
}
