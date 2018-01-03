<?php

namespace Ions\Mvc\Sender;

use Ions\Http\Response;

/**
 * Class PhpEnvironmentResponseSender
 * @package Ions\Mvc\Sender
 */
class PhpEnvironmentResponseSender extends HttpResponseSender
{
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
