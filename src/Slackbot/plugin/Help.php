<?php

namespace Slackbot\plugin;

use Slackbot\Command;
use Slackbot\utility\FormattingUtility;

/**
 * Class Help
 * @package Slackbot\plugin
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
                $response .= "/{$commandName}" . $formattingUtility->newLine() . ' - ' . $commandDetails['description'];
            }
        }

        return $response;
    }
}
