<?php

namespace Ions\Mvc;

use Ions\Event\Event;
use Ions\Router\RouteMatch;

class Action extends Event
{
    const EVENT_BOOTSTRAP      = 'bootstrap';
    const EVENT_DISPATCH       = 'dispatch';
    const EVENT_FINISH         = 'finish';
    const EVENT_ROUTE          = 'route';

    protected $result;
    protected $routeMatch;

    public function getRouteMatch()
    {
        return $this->routeMatch;
    }

    public function setRouteMatch(RouteMatch $matches)
    {
        $this->setParam('route-match', $matches);
        $this->routeMatch = $matches;
        return $this;
    }

    public function getResult()
    {
        return $this->result;
    }

    public function setResult($result)
    {
        $this->setParam('__RESULT__', $result);
        $this->result = $result;
        return $this;
    }

    public function isError()
    {
        return (bool) $this->getParam('error', false);
    }

    public function setError($message)
    {
        $this->setParam('error', $message);
        return $this;
    }

    public function getError()
    {
        return $this->getParam('error', '');
    }

    public function getController()
    {
        return $this->getParam('controller');
    }

    public function setController($name)
    {
        $this->setParam('controller', $name);
        return $this;
    }

    public function getControllerClass()
    {
        return $this->getParam('controller-class');
    }

    public function setControllerClass($class)
    {
        $this->setParam('controller-class', $class);
        return $this;
    }
}
