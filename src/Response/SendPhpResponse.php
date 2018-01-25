<?php

namespace Ions\Mvc\Response;

use Ions\Http\Response;

/**
 * Class PhpEnvResponse
 * @package Ions\Mvc
 */
class SendPhpResponse extends SendHttpResponse
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
        $event->stopPropagation();
        return $this;
    }
}
