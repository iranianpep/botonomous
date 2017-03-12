<?php

namespace Slackbo\Tests;

use Slackbot\Action;
use Slackbot\Channel;
use Slackbot\MessageAction;
use Slackbot\Team;
use Slackbot\User;

class MessageActionTest extends \PHPUnit_Framework_TestCase
{
    public function testLoad()
    {
        $originalMessage = [
            'text'        => 'New comic book alert!',
            'attachments' => [
                0 => [
                    'title'  => 'The Further Adventures of Slackbot',
                    'fields' => [
                        0 => [
                            'title' => 'Volume',
                            'value' => '1',
                            'short' => true,
                        ],
                        1 => [
                            'title' => 'Issue',
                            'value' => '3',
                            'short' => true,
                        ],
                    ],
                    'author_name' => 'Stanford S. Strickland',
                    'author_icon' => 'https://a.slack-edge.com/homepage_custom_integrations-2x.png',
                    'image_url'   => 'http://i.imgur.com/OJkaVOI.jpg?1',
                ],
                1 => [
                    'title' => 'Synopsis',
                    'text'  => 'After @episod pushed exciting changes ...',
                ],
                2 => [
                    'fallback'        => 'Would you recommend it to customers?',
                    'title'           => 'Would you recommend it to customers?',
                    'callback_id'     => 'comic_1234_xyz',
                    'color'           => '#3AA3E3',
                    'attachment_type' => 'default',
                    'actions'         => [
                        0 => [
                            'name'  => 'recommend',
                            'text'  => 'Recommend',
                            'type'  => 'button',
                            'value' => 'recommend',
                        ],
                        1 => [
                            'name'  => 'no',
                            'text'  => 'No',
                            'type'  => 'button',
                            'value' => 'bad',
                        ],
                    ],
                ],
            ],
        ];

        $info = [
            'actions' => [
                    0 => [
                            'name'  => 'recommend',
                            'value' => 'yes',
                            'type'  => 'button',
                        ],
                ],
            'callback_id' => 'comic_1234_xyz',
            'team'        => [
                    'id'     => 'T47563693',
                    'domain' => 'watermelonsugar',
                ],
            'channel' => [
                    'id'   => 'C065W1189',
                    'name' => 'forgotten-works',
                ],
            'user' => [
                    'id'   => 'U045VRZFT',
                    'name' => 'brautigan',
                ],
            'action_ts'        => '1458170917.164398',
            'message_ts'       => '1458170866.000004',
            'attachment_id'    => '1',
            'token'            => 'xAB3yVzGS4BQ3O9FACTa8Ho4',
            'original_message' => $originalMessage,
            'response_url'     => 'https://hooks.slack.com/actions/T47563693/6204672533/x7ZLaiVMoECAW50Gw1ZYAXEM',
        ];

        $messageAction = new MessageAction();
        $messageAction->load($info);

        $actions = [];
        $action = new Action();
        $action->setName('recommend');
        $action->setValue('yes');
        $action->setType('button');
        $actions[] = $action;

        $this->assertEquals($actions, $messageAction->getActions());

        $this->assertEquals('comic_1234_xyz', $messageAction->getCallbackId());

        $team = new Team();
        $team->setSlackId('T47563693');
        $team->setDomain('watermelonsugar');

        $this->assertEquals($team, $messageAction->getTeam());

        $channel = new Channel();
        $channel->setSlackId('C065W1189');
        $channel->setName('forgotten-works');

        $this->assertEquals($channel, $messageAction->getChannel());

        $user = new User();
        $user->setSlackId('U045VRZFT');
        $user->setName('brautigan');

        $this->assertEquals($user, $messageAction->getUser());

        $this->assertEquals('1458170917.164398', $messageAction->getActionTimestamp());
        $this->assertEquals('1458170866.000004', $messageAction->getMessageTimestamp());
        $this->assertEquals('1', $messageAction->getAttachmentId());
        $this->assertEquals('xAB3yVzGS4BQ3O9FACTa8Ho4', $messageAction->getToken());
        $this->assertEquals($originalMessage, $messageAction->getOriginalMessage());
        $this->assertEquals(
            'https://hooks.slack.com/actions/T47563693/6204672533/x7ZLaiVMoECAW50Gw1ZYAXEM',
            $messageAction->getResponseUrl()
        );
    }
}
