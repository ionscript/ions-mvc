<?php

namespace Ions\Mvc\Response;

use Ions\Event\Event;
use Ions\Std\ResponseInterface;

/**
 * Class SendResponseEvent
 * @package Ions\Mvc
 */
class SendResponseEvent extends Event
{
    /**
     * @var string
     */
    protected $name = 'sendResponse';
    /**
     * @var
     */
    protected $response;
    /**
     * @var array
     */
    protected $headersSent = [];
    /**
     * @var array
     */
    protected $contentSent = [];

    /**
     * @param ResponseInterface $response
     * @return $this
     */
    public function setResponse(ResponseInterface $response)
    {
        $this->setParam('response', $response);
        $this->response = $response;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * @return $this
     */
    public function setContentSent()
    {
        $response = $this->getResponse();
        $contentSent = $this->getParam('contentSent', []);
        $contentSent[spl_object_hash($response)] = true;
        $this->setParam('contentSent', $contentSent);
        $this->contentSent[spl_object_hash($response)] = true;

        return $this;
    }

    /**
     * @return bool
     */
    public function contentSent()
    {
        $response = $this->getResponse();
        if (isset($this->contentSent[spl_object_hash($response)])) {
            return true;
        }
        return false;
    }

    /**
     * @return $this
     */
    public function setHeadersSent()
    {
        $response = $this->getResponse();
        $headersSent = $this->getParam('headersSent', []);
        $headersSent[spl_object_hash($response)] = true;
        $this->setParam('headersSent', $headersSent);
        $this->headersSent[spl_object_hash($response)] = true;
        return $this;
    }

    /**
     * @return bool
     */
    public function headersSent()
    {
        $response = $this->getResponse();

        if (isset($this->headersSent[spl_object_hash($response)])) {
            return true;
        }

        return false;
    }
}
