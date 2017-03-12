<?php

namespace Slackbot\utility;

class ClassUtility
{
    public function extractClassNameFromFullName($fullName)
    {
        // get last part of the namespace
        $classParts = explode('\\', $fullName);

        return end($classParts);
    }

    public function loadAttributes($object, $attributes)
    {
        // if $attributes is not array convert it to array
        if (!is_array($attributes)) {
            $attributes = json_decode($attributes, true);
        }

        $stringUtility = new StringUtility();
        foreach ($attributes as $attributeKey => $attributeValue) {
            // For id, we cannot use 'set'.$stringUtility->snakeCaseToCamelCase($attributeKey) since it's named slackId
            if ($attributeKey === 'id') {
                $method = 'setSlackId';
            } else {
                // handle ts because there is setTimestamp instead of setTs
                if ($attributeKey === 'ts' || $stringUtility->endsWith($attributeKey, '_ts')) {
                    // replace the last ts with timestamp
                    $attributeKey = preg_replace('/ts$/', 'timestamp', $attributeKey);
                }

                $method = 'set'.$stringUtility->snakeCaseToCamelCase($attributeKey);
            }

            // ignore if setter function does not exist
            if (!method_exists($object, $method)) {
                continue;
            }

            $object->$method($attributeValue);
        }

        return $object;
    }
}
