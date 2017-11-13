<?php

namespace Botonomous;

use Botonomous\utility\ArrayUtility;
use Botonomous\utility\MessageUtility;
use NlpTools\Stemmers\PorterStemmer;
use NlpTools\Tokenizers\WhitespaceAndPunctuationTokenizer;

/**
 * Class CommandExtractor.
 */
class CommandExtractor
{
    private $config;
    private $error;
    private $messageUtility;
    private $dictionary;
    private $commandContainer;

    /**
     * @param string $message
     *
     * @return Command|null|void
     */
    public function getCommandByMessage(string $message)
    {
        if (empty($message)) {
            $this->setError('Message is empty');

            return;
        }

        /*
         * Process the message and find explicitly specified command
         */
        return $this->getCommandObjectByMessage($message);
    }

    /**
     * @param $message
     *
     * @return array
     */
    public function countKeywordOccurrence(string $message): array
    {
        $stemmer = new PorterStemmer();

        // tokenize $message
        $stemmed = implode(' ', $stemmer->stemAll((new WhitespaceAndPunctuationTokenizer())->tokenize($message)));

        $count = [];
        foreach ($this->getCommandContainer()->getAllAsObject() as $commandKey => $commandObject) {
            $keywordsCount = $this->commandKeywordOccurrence($commandObject, $stemmed);

            $total = 0;
            if (empty($keywordsCount)) {
                $count[$commandKey] = $total;
                continue;
            }

            $count[$commandKey] = array_sum($keywordsCount);
        }

        return $count;
    }

    /**
     * @param Command $command
     * @param string  $message
     *
     * @return array|void
     */
    private function commandKeywordOccurrence(Command $command, string $message)
    {
        $stemmer = new PorterStemmer();
        $keywords = $command->getKeywords();
        if (empty($keywords)) {
            return;
        }

        return $this->getMessageUtility()->keywordCount(
            $stemmer->stemAll($keywords),
            $message
        );
    }

    /**
     * @param $message
     *
     * @return Command|null
     */
    private function getCommandObjectByMessage(string $message)
    {
        $command = $this->getMessageUtility()->extractCommandName($message);
        if (empty($command)) {
            $command = (new ArrayUtility())->maxPositiveValueKey($this->countKeywordOccurrence($message));
        }

        // check command name
        if (empty($command)) {
            // get the default command if no command is find in the message
            $command = $this->checkDefaultCommand();
        }

        return $this->getCommandObjectByCommand($command);
    }

    /**
     * @return mixed|void
     */
    private function checkDefaultCommand()
    {
        $command = $this->getConfig()->get('defaultCommand');

        if (empty($command)) {
            $this->setError($this->getDictionary()->get('generic-messages')['noCommandMessage']);

            return;
        }

        return $command;
    }

    /**
     * @param $command
     *
     * @return Command|void
     */
    private function getCommandObjectByCommand($command)
    {
        if (empty($command)) {
            return;
        }

        $commandObject = $this->getCommandContainer()->getAsObject($command);

        if ($this->validateCommandObject($commandObject) !== true) {
            return;
        }

        return $commandObject;
    }

    /**
     * Validate the command object.
     *
     * @param Command|null $commandObject
     *
     * @return bool
     */
    private function validateCommandObject($commandObject): bool
    {
        // check command details
        if (empty($commandObject)) {
            $this->setError(
                $this->getDictionary()->getValueByKey('generic-messages', 'unknownCommandMessage')
            );

            return false;
        }

        return true;
    }

    /**
     * @return string
     */
    public function getError(): string
    {
        return $this->error;
    }

    /**
     * @param string $error
     */
    public function setError(string $error)
    {
        $this->error = $error;
    }

    /**
     * @return Config
     */
    public function getConfig(): Config
    {
        if ($this->config === null) {
            $this->config = (new Config());
        }

        return $this->config;
    }

    /**
     * @param Config $config
     */
    public function setConfig(Config $config)
    {
        $this->config = $config;
    }

    /**
     * @return MessageUtility
     */
    public function getMessageUtility(): MessageUtility
    {
        if (!isset($this->messageUtility)) {
            $this->setMessageUtility(new MessageUtility());
        }

        return $this->messageUtility;
    }

    /**
     * @param MessageUtility $messageUtility
     */
    public function setMessageUtility(MessageUtility $messageUtility)
    {
        $this->messageUtility = $messageUtility;
    }

    /**
     * @return Dictionary
     */
    public function getDictionary(): Dictionary
    {
        if (!isset($this->dictionary)) {
            $this->setDictionary(new Dictionary());
        }

        return $this->dictionary;
    }

    /**
     * @param Dictionary $dictionary
     */
    public function setDictionary(Dictionary $dictionary)
    {
        $this->dictionary = $dictionary;
    }

    /**
     * @return CommandContainer
     */
    public function getCommandContainer(): CommandContainer
    {
        if (!isset($this->commandContainer)) {
            $this->setCommandContainer(new CommandContainer());
        }

        return $this->commandContainer;
    }

    /**
     * @param CommandContainer $commandContainer
     */
    public function setCommandContainer(CommandContainer $commandContainer)
    {
        $this->commandContainer = $commandContainer;
    }
}
