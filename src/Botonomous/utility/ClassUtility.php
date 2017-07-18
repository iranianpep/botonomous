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
        $camelCase = (new StringUtility())->snakeCaseToCamelCase($this->processTimestamp($attributeKey));

        /**
         * If camel case attribute starts with 'is', 'has', ... following by an uppercase letter, remove it
         * This is used to handle calling functions such as setIm or setUserDeleted
         * instead of setIsIm or setIsUserDeleted.
         *
         * The style checkers complain about functions such as setIsIm, ...
         */
        $function = 'set'.$this->removeBooleanPrefix($camelCase);

        return method_exists($object, $function) ? $function : false;
    }

    /**
     * If text is 'ts' or ends with '_ts' replace it with 'timestamp'.
     *
     * @param $text
     *
     * @return mixed
     */
    private function processTimestamp($text)
    {
        if ($text === 'ts' || (new StringUtility())->endsWith($text, '_ts')) {
            // replace the last ts with timestamp
            $text = preg_replace('/ts$/', 'timestamp', $text);
        }

        return $text;
    }

    /**
     * Check if the text starts with boolean prefixes such as 'is', 'has', ...
     *
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

    /**
     * If find the boolean prefix, remove it.
     *
     * @param $text
     *
     * @return string
     */
    private function removeBooleanPrefix($text)
    {
        $booleanPrefix = $this->findBooleanPrefix($text);
        if (!empty($booleanPrefix)) {
            // found the boolean prefix - remove it
            $text = substr($text, strlen($booleanPrefix));
        }

        return $text;
    }
}
