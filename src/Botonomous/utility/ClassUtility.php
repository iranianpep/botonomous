<?php

namespace Botonomous\utility;

use Botonomous\AbstractBaseSlack;

/**
 * Class ClassUtility.
 */
class ClassUtility
{
    /**
     * @param string $fullName
     *
     * @return mixed
     */
    public function extractClassNameFromFullName($fullName)
    {
        // get last part of the namespace
        $classParts = explode('\\', $fullName);

        return end($classParts);
    }

    /**
     * @param AbstractBaseSlack $object
     * @param                   $attributes
     *
     * @return AbstractBaseSlack
     */
    public function loadAttributes(AbstractBaseSlack $object, $attributes)
    {
        // if $attributes is not array convert it to array
        if (!is_array($attributes)) {
            $attributes = json_decode($attributes, true);
        }

        foreach ($attributes as $attributeKey => $attributeValue) {
            $method = $this->getSetMethodByAttribute($object, $attributeKey);

            // ignore if setter function does not exist
            if (empty($method)) {
                continue;
            }

            $object->$method($attributeValue);
        }

        return $object;
    }

    /**
     * @param AbstractBaseSlack $object
     * @param $attributeKey
     *
     * @return bool|string
     */
    private function getSetMethodByAttribute(AbstractBaseSlack $object, $attributeKey)
    {
        // For id, we cannot use 'set'.$stringUtility->snakeCaseToCamelCase($attributeKey) since it's named slackId
        if ($attributeKey === 'id') {
            return 'setSlackId';
        }

        // handle ts because there is setTimestamp instead of setTs
        $stringUtility = new StringUtility();
        if ($attributeKey === 'ts' || $stringUtility->endsWith($attributeKey, '_ts')) {
            // replace the last ts with timestamp
            $attributeKey = preg_replace('/ts$/', 'timestamp', $attributeKey);
        }

        $camelCase = $stringUtility->snakeCaseToCamelCase($attributeKey);

        /**
         * If camel case attribute starts with 'is', 'has', ... following by an uppercase letter, remove it
         * This is used to handle calling functions such as setIm or setUserDeleted
         * instead of setIsIm or setIsUserDeleted.
         *
         * The style checkers complain about functions such as setIsIm, ...
         */
        $booleanPrefix = $this->findBooleanPrefix($camelCase);
        if (!empty($booleanPrefix)) {
            // found the boolean prefix - remove it
            $camelCase = substr($camelCase, strlen($booleanPrefix));
        }

        $function = 'set'.$camelCase;

        if (method_exists($object, $function)) {
            return $function;
        }

        return false;
    }

    /**
     * @param $text
     *
     * @return mixed
     */
    private function findBooleanPrefix($text)
    {
        $booleanPrefixes = ['is', 'has'];
        sort($booleanPrefixes);

        foreach ($booleanPrefixes as $booleanPrefix) {
            if (!preg_match('/^((?i)'.$booleanPrefix.')[A-Z0-9]/', $text)) {
                continue;
            }

            return $booleanPrefix;
        }
    }
}
