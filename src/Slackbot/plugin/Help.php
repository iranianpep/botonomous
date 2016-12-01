<?php

namespace Slackbot\plugin;

use Slackbot\Command;
use Slackbot\utility\FormattingUtility;

class Help extends AbstractPlugin
{
    public function index()
    {
        $allCommands = (new Command())->getAll();

        $response = '';
        if (!empty($allCommands)) {
            $formattingUtility = (new FormattingUtility());

            foreach ($allCommands as $commandName => $commandDetails) {
                $response .= "/{$commandName}" . $formattingUtility->newLine();
            }
        }

        return $response;
    }
}
