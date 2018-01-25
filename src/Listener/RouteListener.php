<?php

namespace Ions\Mvc\Listener;


use Ions\Event\EventManagerInterface;
use Ions\Event\ListenerInterface;
use Ions\Event\ListenerTrait;
use Ions\Mvc\Action;
use Ions\Mvc\Application;
use Ions\Router\RouteMatch;

/**
 * Class RouteListener
 * @package Ions\Mvc\Listener
 */
class RouteListener implements ListenerInterface
{
    use ListenerTrait;

    /**
     * @param EventManagerInterface $events
     * @param int $priority
     * @return void
     */
    public function attach(EventManagerInterface $events, $priority = 1)
    {
        $this->listeners[] = $events->attach('route', [$this, 'onRoute']);
    }

    /**
     * @param Action $event
     * @return mixed
     */
    public function onRoute(Action $event)
    {
        $services = $event->getTarget()->getServiceManager();

        $request = $services->get('request');
        $router = $services->get('router');

        if(!$routeMatch = $router->match($request)) {
            $routeMatch = $router->match(Application::ERROR_NOT_FOUND);
        }

        if ($routeMatch instanceof RouteMatch) {
            $event->setRouteMatch($routeMatch);
        }

        return $routeMatch;
    }
}
