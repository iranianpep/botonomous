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
        $allCommands = $this->getSlackbot()->getCommands();

        $response = '';
        if (!empty($allCommands)) {
            $formattingUtility = (new FormattingUtility());

            foreach ($allCommands as $commandName => $commandObject) {
                if (!$commandObject instanceof Command) {
                    continue;
                }

                $response .= "/{$commandName} - ".$commandObject->getDescription().$formattingUtility->newLine();
            }
        }

        return $response;
    }
}
