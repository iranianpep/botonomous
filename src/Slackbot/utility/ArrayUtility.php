<?php

namespace Slackbot\utility;

class ArrayUtility extends AbstractUtility
{
    /**
     * @param array $toFilter
     * @param array $keepKeys Includes the keys that need to be kept in $toFilter array
     *
     * @return array
     */
    public function filterArray(array $toFilter, array $keepKeys)
    {
        return array_intersect_key($toFilter, array_flip($keepKeys));
    }
}
