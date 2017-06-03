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
     * Check if key is present in $search array and has got a value only if the value is string.
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

    /**
     * Set a value in a nested array based on path.
     *
     * @param array $array The array to modify
     * @param array $path  The path in the array
     * @param mixed $value The value to set
     *
     * @return void
     */
    public function setNestedArrayValue(&$array, $path, &$value)
    {
        $current = &$array;
        foreach ($path as $key) {
            $current = &$current[$key];
        }

        $current = $value;
    }

    /**
     * Get a value in a nested array based on path
     * See https://stackoverflow.com/a/9628276/419887.
     *
     * @param array $array The array to modify
     * @param array $path  The path in the array
     *
     * @return mixed
     */
    public function getNestedArrayValue(&$array, $path)
    {
        $current = &$array;
        foreach ($path as $key) {
            $current = &$current[$key];
        }

        return $current;
    }
}
