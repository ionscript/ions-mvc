<?php

namespace Ions\Mvc\Sender;

/**
 * Interface ResponseSenderInterface
 * @package Ions\Mvc\Sender
 */
interface ResponseSenderInterface
{
    /**
     * @param SendResponseEvent $event
     * @return mixed
     */
    public function __invoke(SendResponseEvent $event);
}
