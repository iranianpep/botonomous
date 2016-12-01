<?php

namespace Slackbot\plugin;

class Ping extends AbstractPlugin
{
    public function index()
    {
        return 'pong';
    }
    
    public function pong()
    {
        return 'ping';
    }
}
