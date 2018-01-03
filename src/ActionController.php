<?php

namespace Ions\Mvc;

abstract class ActionController extends Controller
{
    /**
     * @var string
     */
    protected $identifier = __CLASS__;

    /**
     * @return void
     */
    public function indexAction()
    {
        $this->response->setContent('Placeholder page');
    }

    /**
     * @param MvcEvent $e
     * @return mixed
     * @throws \DomainException
     */
    public function onDispatch(MvcEvent $e)
    {
        $routeMatch = $e->getRouteMatch();

        if (! $routeMatch) {
            throw new \DomainException('Missing route matches; unsure how to retrieve action');
        }

        $action = $routeMatch->getParam('action', 'not-found');
        $method = static::getMethodFromAction($action);

        if (! method_exists($this, $method)) {
            $method = 'notFoundAction';
        }

        $actionResponse = $this->$method();

        $e->setResult($actionResponse);

        return $actionResponse;
    }

    /**
     * @return void
     */
    public function notFoundAction()
    {
        $event      = $this->getEvent();
        $routeMatch = $event->getRouteMatch();
        $routeMatch->setParam('action', 'not-found');

        $this->response->setStatusCode(404);
        $this->response->setContent('Page not found');
    }
}
