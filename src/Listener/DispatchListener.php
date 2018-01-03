<?php

namespace Ions\Mvc\Listener;

use Ions\Event\Listener;
use Ions\Event\EventManagerInterface;
use Ions\Mvc\MvcEvent;
use Ions\Mvc\ServiceManager;
use Ions\Route\RouteMatch;

/**
 * Class DispatchListener
 * @package Ions\Mvc\Listener
 */
class DispatchListener extends Listener
{
    /**
     * @var
     */
    private $services;

    /**
     * @param ServiceManager $serviceManager
     * @param EventManagerInterface $events
     * @param int $priority
     * @return null|void
     * @throws \Exception
     */
    public function attach(ServiceManager $serviceManager, EventManagerInterface $events, $priority = 1)
    {
        $this->services = $serviceManager;
        $this->listeners[] = $events->attach('dispatch', [$this, 'onDispatch']);
    }

    public function onDispatch(MvcEvent $event)
    {
        if (null !== ($return = $event->getResult())) {
            return null;
        }

        $routeMatch = $event->getRouteMatch();
        $controllerName = $routeMatch instanceof RouteMatch ? $routeMatch->getParam('controller', 'error-not-found') : 'error-not-found';

        try {
            $controller = $this->services->get($controllerName);
        } catch (\Exception $exception) {
            throw $exception;
        }

        if ($controller) {
            $controller->setEvent($event);
        }

        try {
            return $controller->dispatch();
        } catch (\Exception $ex) {
            throw $ex;
        }
    }
}
