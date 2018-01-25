<?php

namespace Ions\Mvc\Response;

use Ions\Http\Response;

/**
 * Class HttpResponse
 * @package Ions\Mvc
 */
class SendHttpResponse extends AbstractSendResponse
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

        $event->stopPropagation();

        return $this;
    }
}
