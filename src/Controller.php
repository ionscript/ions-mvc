<?php

namespace Ions\Mvc;

use Ions\Event;

/**
 * Class Controller
 * @package Ions\Mvc
 */
abstract class Controller extends ServiceManager
{
    /**
     * @var string
     */
    protected $identifier = __CLASS__;

    /**
     * @var
     */
    protected $event;

    /**
     * @var
     */
    protected $events;

    /**
     * @return void
     */
    public function indexAction()
    {
        $this->response->setContent('Placeholder page');
    }

    /**
     * @param Action $e
     * @return mixed
     * @throws \DomainException
     */
    public function onDispatch(Action $e)
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
     * @throws \InvalidArgumentException
     */
    public function notFoundAction()
    {
        $event      = $this->getEvent();
        $routeMatch = $event->getRouteMatch();
        $routeMatch->setParam('action', 'not-found');

        $this->response->setStatusCode(404);
        $this->response->setContent('Action not found');
    }

    /**
     * @param $route
     * @return mixed
     * @throws \RuntimeException
     */
    public function model($route)
    {
        if (isset($this->{$route})) {
            return $this->{$route};
        }

        $class = $this->app->getName() . '\\' . static::getModelFromRoute($route);

        if (class_exists($class)) {
            $this->{$route} = new $class;
        } else {
            throw new \RuntimeException(sprintf(
                'Could not load model %s!',
                $route
            ));
        }

        return $this->{$route};
    }

    /**
     * @param $route
     * @param $data
     * @param $action
     * @return mixed
     */
    public function controller($route, $data = [], $action = 'index')
    {
        $controller = $this->get($route);

        $method = static::getMethodFromAction($action);

        return $controller->$method($data);
    }
    public function view($route, array $data = [])
    {
        $data = array_merge($this->language->get(), $data);

        $theme = $this->config->get('config_view_theme');
        $res = $this->view->render('app/'.$this->app->getName() . '/view/theme/' . $theme . '/' . $route . '.tpl', $data);

        return $res;
    }

    /**
     * @return mixed
     * @throws \InvalidArgumentException
     */
    public function dispatch()
    {
        $event = $this->getEvent();
        $event->setName('dispatch');
        $this->getEventManager()->triggerEvent($event);
        return $event->getResult();
    }

    /**
     * @param Event\EventManagerInterface $events
     * @return $this
     * @throws \InvalidArgumentException
     */
    public function setEventManager(Event\EventManagerInterface $events)
    {
        $this->events = $events;
        $this->attachDefaultListeners();

        return $this;
    }

    /**
     * @return mixed
     * @throws \InvalidArgumentException
     */
    public function getEventManager()
    {
        if (!$this->events) {
            $this->setEventManager(new Event\EventManager());
        }

        return $this->events;
    }

    /**
     * @param Event\Event $event
     * @throws \InvalidArgumentException
     */
    public function setEvent(Event\Event $event)
    {
        if (!$event instanceof Action) {
            $eventParams = $event->getParams();
            $event = new Action;
            $event->setParams($eventParams);
            unset($eventParams);
        }

        $this->event = $event;
    }

    /**
     * @return mixed
     * @throws \InvalidArgumentException
     */
    public function getEvent()
    {
        if (!$this->event) {
            $this->setEvent(new Action);
        }

        return $this->event;
    }

    /**
     * @return void
     * @throws \InvalidArgumentException
     */
    protected function attachDefaultListeners()
    {
        $events = $this->getEventManager();
        $events->attach('dispatch', [$this, 'onDispatch']);
    }

    /**
     * @param $action
     * @return mixed|string
     */
    public static function getMethodFromAction($action)
    {
        $method = str_replace(['.', '-', '_'], ' ', $action);
        $method = ucwords($method);
        $method = str_replace(' ', '', $method);
        $method = lcfirst($method);
        $method .= 'Action';

        return $method;
    }

    /**
     * @param string $route
     * @return mixed|string
     */
    public static function getModelFromRoute($route)
    {
        $model = str_replace(['.', '-', '_'], ' ', $route);
        $model = ucwords($model, '/ ');
        $model = str_replace([' ', '/'], ['', '\\'], $model);
        $model .= strpos($model, 'Model') ? '' : 'Model';

        return $model;
    }
}
