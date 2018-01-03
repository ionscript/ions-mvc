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
     * @var
     */
    protected $event;

    /**
     * @var
     */
    protected $events;

    /**
     * @param $route
     * @return array|mixed|object|string
     * @throws \RuntimeException
     */
    public function model($route)
    {
        if (isset($this->{$route})) {
            return $this->{$route};
        }

        $parts = explode('/', $route);

        $class = implode('\\', array_map('ucfirst', $parts)) . 'Model';

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
     * @return mixed
     */
    public function controller($route)
    {
        $controller = $this->get($route);

        $method = static::getMethodFromAction('index');

        return $controller->$method();
    }

    public function view($route, array $data = [])
    {
        $data = array_merge($this->language->get(), $data);

        $theme = $this->config->get('config_view_theme');
        $res = $this->view->render($this->app->getDirectory() . '/view/theme/' . $theme . '/' . $route . '.tpl', $data);

        return $res;
    }

    /**
     * @return mixed
     */
    public function dispatch()
    {
        $event = $this->getEvent();
        $event->setName('dispatch');
        $event->setTarget($this);
        $this->getEventManager()->triggerEvent($event);
        return $event->getResult();
    }

    /**
     * @param Event\EventManagerInterface $events
     * @return $this
     */
    public function setEventManager(Event\EventManagerInterface $events)
    {
        $this->events = $events;
        $this->attachDefaultListeners();

        return $this;
    }

    /**
     * @return mixed
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
     */
    public function setEvent(Event\Event $event)
    {
        if (!$event instanceof MvcEvent) {
            $eventParams = $event->getParams();
            $event = new MvcEvent;
            $event->setParams($eventParams);
            unset($eventParams);
        }

        $this->event = $event;
    }

    /**
     * @return mixed
     */
    public function getEvent()
    {
        if (!$this->event) {
            $this->setEvent(new MvcEvent);
        }

        return $this->event;
    }

    /**
     * @return void
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
}
