<?php

namespace Slackbot\utility;

use Slackbot\AbstractBaseSlack;

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
            $method = $this->getMethodByAttribute($attributeKey);

            // ignore if setter function does not exist
            if (!method_exists($object, $method)) {
                continue;
            }

            $object->$method($attributeValue);
        }

        return $object;
    }

    /**
     * @param $attributeKey
     *
     * @return string
     */
    private function getMethodByAttribute($attributeKey)
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

        $method = 'set'.$stringUtility->snakeCaseToCamelCase($attributeKey);

        return $method;
    }
}
