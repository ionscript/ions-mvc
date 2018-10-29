<?php

namespace Ions\Mvc\Response;

use Ions\Http\Header\HeaderInterface;

/**
 * Class AbstractResponse
 * @package Ions\Mvc
 */
abstract class AbstractSendResponse implements SendResponseInterface
{
    /**
     * @param SendResponseEvent $event
     * @return $this
     */
    public function sendHeaders(SendResponseEvent $event)
    {
        if (headers_sent() || $event->headersSent()) {
            return $this;
        }

        $response = $event->getResponse();

        foreach ($response->getHeaders()->getHeaders() as $header) {
            if ($header instanceof HeaderInterface) {
                header($header->toString(), false);
                continue;
            }
            header($header->toString());
        }

        $status = $response->renderStatusLine();
        header($status);

        $event->setHeadersSent();
        return $this;
    }
}
