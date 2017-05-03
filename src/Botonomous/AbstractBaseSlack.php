<?php

namespace Botonomous;

use Botonomous\utility\ClassUtility;

/**
 * Class AbstractBaseSlack.
 */
abstract class AbstractBaseSlack
{
    /**
     * @param $info
     *
     * @return AbstractBaseSlack
     */
    public function load($info)
    {
        return (new ClassUtility())->loadAttributes($this, $info);
    }
}
