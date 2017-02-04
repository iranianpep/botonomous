<?php

namespace Slackbot\utility;

/**
 * Class RequestUtility.
 */
class RequestUtility
{
    private $content;
    private $post;

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
        return filter_input_array(INPUT_GET);
    }

    /**
     * @return mixed
     */
    public function getServerProtocol()
    {
        return filter_input(INPUT_SERVER, 'SERVER_PROTOCOL', FILTER_SANITIZE_STRING);
    }
}
