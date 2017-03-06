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
}
