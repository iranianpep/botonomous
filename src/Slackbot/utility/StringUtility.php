<?php

namespace Slackbot\utility;

class StringUtility extends AbstractUtility
{
    /**
     * @param $json
     *
     * @return array|mixed
     * @throws \Exception
     */
    public function jsonToArray($json)
    {
        $array = empty($json) ? [] : json_decode($json, true);

        if ($array === null || !is_array($array) || json_last_error() !== 0) {
            throw new \Exception('Invalid JSON content');
        }

        return $array;
    }
}
