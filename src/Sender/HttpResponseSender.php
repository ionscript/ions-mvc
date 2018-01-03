<?php

namespace Ions\Mvc\Sender;

use Ions\Http\Response;

/**
 * Class HttpResponseSender
 * @package Ions\Mvc\Sender
 */
class HttpResponseSender extends AbstractResponseSender
{
    /**
     * @param SendResponseEvent $event
     * @return $this
     */
    public function sendContent(SendResponseEvent $event)
    {
        if ($event->contentSent()) {
            return $this;
        }

        $response = $event->getResponse();

        echo $response->getContent();

        $event->setContentSent();

        return $this;
    }

    /**
     * @param SendResponseEvent $event
     * @return $this
     */
    public function __invoke(SendResponseEvent $event)
    {
        $response = $event->getResponse();

        if (! $response instanceof Response) {
            return $this;
        }

        $this->sendHeaders($event)->sendContent($event);

        $event->stopPropagation(true);

        return $this;
    }
}
