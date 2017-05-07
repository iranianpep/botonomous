<?php

namespace Botonomous\utility;

/**
 * Class ArrayUtility.
 */
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

    /**
     * Check if key is present in $search array and has got a value only if the value is string
     * It considers values such as 0, '0', true and false AS true
     *
     * @param       $key
     * @param array $search
     *
     * @return bool
     */
    public function arrayKeyValueExists($key, array $search)
    {
        if (!array_key_exists($key, $search)) {
            return false;
        }

        if (is_string($search[$key])) {
            return strlen(trim($search[$key])) > 0;
        }

        return true;
    }
}
