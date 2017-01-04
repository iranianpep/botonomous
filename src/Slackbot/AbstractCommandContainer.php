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
    public function get($key)
    {
        $commands = $this->getAll($key);

        if (!array_key_exists($key, $commands)) {
            /** @noinspection PhpInconsistentReturnPointsInspection */
            return;
        }

        return $commands[$key];
    }

    /**
     * @param null $key
     *
     * @throws \Exception
     *
     * @return mixed
     */
    public function getAll($key = null)
    {
        $commands = static::$commands;

        if (!empty($commands)) {
            foreach ($commands as $commandKey => $commandDetails) {
                if (!empty($key) && $commandKey !== $key) {
                    continue;
                }

                $commandDetails['key'] = $commandKey;

                $mappedObject = $this->mapToCommandObject($commandDetails);

                if (empty($mappedObject)) {
                    continue;
                }

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
        if (!isset($row['key'])) {
            throw new \Exception('Key must be provided');
        }

        $mappedObject = new Command($row['key']);

        unset($row['key']);

        if (!empty($row)) {
            foreach ($row as $key => $value) {
                $mappedObject->{'set'.ucwords($key)}($value);
            }
        }

        return $mappedObject;
    }
}
