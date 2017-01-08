<?php

namespace Slackbot;

/**
 * Class AbstractCommandContainer.
 */
abstract class AbstractCommandContainer
{
    protected static $commands;

    /**
     * @param $key
     *
     * @return null
     */
    public function getAsObject($key)
    {
        $commands = $this->getAllAsObject($key);

        if (!array_key_exists($key, $commands)) {
            /* @noinspection PhpInconsistentReturnPointsInspection */
            return;
        }

        return $commands[$key];
    }

    /**
     * @param $commands
     */
    public function setAll($commands)
    {
        static::$commands = $commands;
    }

    /**
     * @return array
     */
    public function getAll()
    {
        return static::$commands;
    }

    /**
     * @param null $key
     *
     * @throws \Exception
     *
     * @return mixed
     */
    public function getAllAsObject($key = null)
    {
        $commands = $this->getAll();

        if (!empty($commands)) {
            foreach ($commands as $commandKey => $commandDetails) {
                if (!empty($key) && $commandKey !== $key) {
                    continue;
                }

                $commandDetails['key'] = $commandKey;

                $mappedObject = $this->mapToCommandObject($commandDetails);

                $commands[$commandKey] = $mappedObject;
            }
        }

        return $commands;
    }

    /**
     * @param array $row
     *
     * @throws \Exception
     *
     * @return Command
     */
    private function mapToCommandObject(array $row)
    {
        $mappedObject = new Command($row['key']);

        unset($row['key']);

        if (!empty($row)) {
            foreach ($row as $key => $value) {
                $methodName = 'set'.ucwords($key);

                // check setter exists
                if (!method_exists($mappedObject, $methodName)) {
                    continue;
                }

                $mappedObject->$methodName($value);
            }
        }

        return $mappedObject;
    }
}
