<?php

namespace Slackbot\utility;

/**
 * Class RequestUtility.
 */
class RequestUtility
{
    private $content;
    private $post;
    private $get;

    /**
     * @return string
     */
    public function getContent()
    {
        if (isset($this->content)) {
            return $this->content;
        }

        return file_get_contents('php://input');
    }

    /**
     * @param $content
     */
    public function setContent($content)
    {
        $this->content = $content;
    }

    /**
     * @return mixed
     */
    public function getPost()
    {
        if (isset($this->post)) {
            return $this->post;
        }

        return filter_input_array(INPUT_POST);
    }

    /**
     * @param array $post
     */
    public function setPost(array $post)
    {
        $this->post = $post;
    }

    /**
     * @return mixed
     */
    public function getGet()
    {
        if (isset($this->get)) {
            return $this->get;
        }

        return filter_input_array(INPUT_GET);
    }

    /**
     * @param array $get
     */
    public function setGet(array $get)
    {
        $this->get = $get;
    }

    /**
     * @return mixed
     */
    public function getServerProtocol()
    {
        return filter_input(INPUT_SERVER, 'SERVER_PROTOCOL', FILTER_SANITIZE_STRING);
    }
}
