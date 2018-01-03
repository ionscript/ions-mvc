<?php

namespace Ions\Mvc\Sender;

use Ions\Http\Header\HeaderInterface;

/**
 * Class AbstractResponseSender
 * @package Ions\Mvc\Sender
 */
abstract class AbstractResponseSender implements ResponseSenderInterface
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

        foreach ($response->getHeaders() as $header) {
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
