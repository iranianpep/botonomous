<?php

namespace Slackbot\plugin\help;

use Slackbot\Command;
use Slackbot\CommandContainer;
use Slackbot\plugin\AbstractPlugin;
use Slackbot\utility\FormattingUtility;

/**
 * Class Help.
 */
class Help extends AbstractPlugin
{
    /**
     * @return string
     */
    public function index()
    {
        $allCommands = (new CommandContainer())->getAll();

        $response = '';
        if (!empty($allCommands)) {
            $formattingUtility = (new FormattingUtility());

            foreach ($allCommands as $commandName => $commandObject) {
                if (!$commandObject instanceof Command) {
                    continue;
                }

                $response .= "/{$commandName}".$formattingUtility->newLine().' - '.$commandObject->getDescription();
            }
        }

        return $response;
    }
}
