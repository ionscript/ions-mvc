<?php

namespace Ions\Mvc\Listener;

use Ions\Event\Listener;
use Ions\Event\EventManagerInterface;
use Ions\Mvc\MvcEvent;
use Ions\Mvc\Application;
use Ions\Mvc\ServiceManager;
use Ions\Route\RouteMatch;

/**
 * Class RouteListener
 * @package Ions\Mvc\Listener
 */
class RouteListener extends Listener
{
    /**
     * @var
     */
    private $services;

    /**
     * @param ServiceManager $serviceManager
     * @param EventManagerInterface $events
     * @param int $priority
     * @return void
     */
    public function attach(ServiceManager $serviceManager, EventManagerInterface $events, $priority = 1)
    {
        $this->services = $serviceManager;
        $this->listeners[] = $events->attach('route', [$this, 'onRoute']);
    }

    /**
     * @param MvcEvent $event
     * @return mixed
     */
    public function onRoute(MvcEvent $event)
    {
        $request = $this->services->get('request');
        $router = $this->services->get('router');

        if(!$routeMatch = $router->match($request)) {
            $routeMatch = $router->match(Application::ERROR_NOT_FOUND);
        }

        if ($routeMatch instanceof RouteMatch) {
            $event->setRouteMatch($routeMatch);
        }

        return $routeMatch;
    }
}
