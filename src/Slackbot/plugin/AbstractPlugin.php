<?php

namespace Slackbot\plugin;

abstract class AbstractPlugin implements PluginInterface
{
    protected $request;

    public function __construct($request)
    {
        $this->setRequest($request);
    }

    /**
     * @return mixed
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @param mixed $request
     */
    public function setRequest($request)
    {
        $this->request = $request;
    }
}
