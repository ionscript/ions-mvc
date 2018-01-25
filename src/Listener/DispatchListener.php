<?php

namespace Ions\Mvc\Listener;

use Ions\Event\EventManagerInterface;
use Ions\Event\ListenerInterface;
use Ions\Event\ListenerTrait;
use Ions\Mvc\Action;
use Ions\Router\RouteMatch;

/**
 * Class DispatchListener
 * @package Ions\Mvc\Listener
 */
class DispatchListener implements ListenerInterface
{
    use ListenerTrait;

    /**
     * @param EventManagerInterface $events
     * @param int $priority
     * @return null|void
     * @throws \Exception
     */
    public function attach(EventManagerInterface $events, $priority = 1)
    {
        $this->listeners[] = $events->attach('dispatch', [$this, 'onDispatch']);
    }

    public function onDispatch(Action $event)
    {
        if (null !== ($return = $event->getResult())) {
            return null;
        }

        $routeMatch = $event->getRouteMatch();
        $controllerName = $routeMatch instanceof RouteMatch ? $routeMatch->getParam('controller', 'error-not-found') : 'error-not-found';

        $services = $event->getTarget()->getServiceManager();

        try {
            $controller = $services->get($controllerName);
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
