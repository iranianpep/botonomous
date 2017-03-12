<?php

namespace Slackbo\Tests;

use Slackbot\MessageAction;

class MessageActionTest extends \PHPUnit_Framework_TestCase
{
    public function testLoad()
    {
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
            'original_message' => [
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
                                    'author_icon' => 'https://api.slack.comhttps://a.slack-edge.com/bfaba/img/api/homepage_custom_integrations-2x.png',
                                    'image_url'   => 'http://i.imgur.com/OJkaVOI.jpg?1',
                                ],
                            1 => [
                                    'title' => 'Synopsis',
                                    'text'  => 'After @episod pushed exciting changes to a devious new branch back in Issue 1, Slackbot notifies @don about an unexpected deploy...',
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
                ],
            'response_url' => 'https://hooks.slack.com/actions/T47563693/6204672533/x7ZLaiVMoECAW50Gw1ZYAXEM',
        ];

        $messageAction = new MessageAction();
        $messageAction->load($info);

        $this->assertEquals('comic_1234_xyz', $messageAction->getCallbackId());
        $this->assertEquals('1458170917.164398', $messageAction->getActionTimestamp());
    }
}
