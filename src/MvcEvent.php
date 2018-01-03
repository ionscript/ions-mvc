<?php

namespace Ions\Mvc;

use Ions\Event\Event;
use Ions\Route\RouteMatch;

/**
 * Class MvcEvent
 * @package Ions\Mvc
 */
class MvcEvent extends Event
{
    /**
     * @var
     */
    protected $app;
    /**
     * @var
     */
    protected $services;
    /**
     * @var
     */
    protected $result;
    /**
     * @var
     */
    protected $routeMatch;

    /**
     * @param $app
     * @return $this
     */
    public function setApp($app)
    {
        $this->setParam('app', $app);
        $this->app = $app;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getApp()
    {
        return $this->app;
    }

    /**
     * @return mixed
     */
    public function getServiceManager()
    {
        return $this->services;
    }

    /**
     * @param ServiceManager $serviceManager
     * @return $this
     */
    public function setServiceManager(ServiceManager $serviceManager)
    {
        $this->setParam('services', $serviceManager);
        $this->services = $serviceManager;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getRouteMatch()
    {
        return $this->routeMatch;
    }

    /**
     * @param RouteMatch $matches
     * @return $this
     */
    public function setRouteMatch(RouteMatch $matches)
    {
        $this->setParam('route-match', $matches);
        $this->routeMatch = $matches;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getResult()
    {
        return $this->result;
    }

    /**
     * @param $result
     * @return $this
     */
    public function setResult($result)
    {
        $this->setParam('__RESULT__', $result);
        $this->result = $result;
        return $this;
    }

    /**
     * @return bool
     */
    public function isError()
    {
        return (bool)$this->getParam('error', false);
    }

    /**
     * @param $message
     * @return $this
     */
    public function setError($message)
    {
        $this->setParam('error', $message);
        return $this;
    }

    /**
     * @return mixed|null
     */
    public function getError()
    {
        return $this->getParam('error', '');
    }

    /**
     * @return mixed|null
     */
    public function getController()
    {
        return $this->getParam('controller');
    }

    /**
     * @param $name
     * @return $this
     */
    public function setController($name)
    {
        $this->setParam('controller', $name);
        return $this;
    }

    /**
     * @return mixed|null
     */
    public function getControllerClass()
    {
        return $this->getParam('controller-class');
    }

    /**
     * @param $class
     * @return $this
     */
    public function setControllerClass($class)
    {
        $this->setParam('controller-class', $class);
        return $this;
    }
}
