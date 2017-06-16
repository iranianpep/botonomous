<?php

namespace Botonomous\client;

use Botonomous\utility\ArrayUtility;

/**
 * Class AbstractClient.
 */
abstract class AbstractClient
{
    private $arrayUtility;

    /**
     * @return ArrayUtility
     */
    public function getArrayUtility()
    {
        if (!isset($this->arrayUtility)) {
            $this->setArrayUtility(new ArrayUtility());
        }

        return $this->arrayUtility;
    }

    /**
     * @param ArrayUtility $arrayUtility
     */
    public function setArrayUtility(ArrayUtility $arrayUtility)
    {
        $this->arrayUtility = $arrayUtility;
    }

    abstract public function apiCall($method, array $arguments = []);
}
