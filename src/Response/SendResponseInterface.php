<?php

namespace Ions\Mvc\Response;

/**
 * Interface ResponseInterface
 * @package Ions\Mvc
 */
interface SendResponseInterface
{
    /**
     * @param SendResponseEvent $event
     * @return mixed
     */
    public function __invoke(SendResponseEvent $event);
}
