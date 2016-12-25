<?php

namespace Slackbot\plugin\help;

use Slackbot\Command;
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
        $allCommands = (new Command())->getAll();

        $response = '';
        if (!empty($allCommands)) {
            $formattingUtility = (new FormattingUtility());

            foreach ($allCommands as $commandName => $commandDetails) {
                $response .= "/{$commandName}".$formattingUtility->newLine().' - '.$commandDetails['description'];
            }
        }

        return $response;
    }
}
