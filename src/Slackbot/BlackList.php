<?php

namespace Slackbot;

class BlackList
{
    private $request;
    private $dictionary;

    public function __construct($request)
    {
        $this->setRequest($request);
    }

    public function isBlackListed()
    {
        // currently isUsernameBlackListed is the only function, but later a new one might be added
        return $this->isUsernameBlackListed();
    }

    public function isUsernameBlackListed()
    {
        // get user name in the request
        $request = $this->getRequest();

        // currently if user_name is not set we do not check it
        if (!isset($request['user_name'])) {
            return false;
        }
        
        // user_name is set, load the blacklist to start checking
        $blacklist = $this->getDictionary()->get('blacklist');

        // currently if username is not set we do not check it
        if (!isset($blacklist['username'])) {
            return false;
        }

        if (in_array($request['user_name'], $blacklist['username'])) {
            return true;
        }

        return false;
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

    /**
     * @return Dictionary
     */
    public function getDictionary()
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
}
