<?php

namespace Ions\Mvc\Sender;

use Ions\Http\Client\Stream;

/**
 * Class SimpleStreamResponseSender
 * @package Ions\Mvc\Sender
 */
class SimpleStreamResponseSender extends AbstractResponseSender
{
    /**
     * @param SendResponseEvent $event
     * @return $this
     */
    public function sendStream(SendResponseEvent $event)
    {
        if ($event->contentSent()) {
            return $this;
        }

        $response = $event->getResponse();
        $stream   = $response->getStream();
        fpassthru($stream);
        $event->setContentSent();
    }

    /**
     * @param SendResponseEvent $event
     * @return $this
     */
    public function __invoke(SendResponseEvent $event)
    {
        $response = $event->getResponse();

        if (! $response instanceof Stream) {
            return $this;
        }

        $this->sendHeaders($event);
        $this->sendStream($event);
        $event->stopPropagation(true);
        return $this;
    }
}
