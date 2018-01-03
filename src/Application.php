<?php

namespace Ions\Mvc;

use Ions\Event;

/**
 * Class Application
 * @package Ions\Mvc
 */
class Application
{
    const ERROR_NOT_FOUND = 'error-not-found';

    /**
     * @var
     */
    protected $event;

    /**
     * @var array|mixed|object|string
     */
    protected $events;

    /**
     * @var ServiceManager
     */
    protected $services;

    /**
     * @var array
     */
    protected $listeners = [
        'RouteListener' => Listener\RouteListener::class,
        'DispatchListener' => Listener\DispatchListener::class,
        'SendResponseListener' => Listener\SendResponseListener::class,
    ];

    /**
     * Application constructor.
     * @param ServiceManager $services
     * @param Event\EventManagerInterface|null $events
     */
    public function __construct(ServiceManager $services, Event\EventManagerInterface $events = null)
    {
        $this->services = $services;
        $this->events = $events ?: $services->get('events');
    }

    /**
     * @param array $configuration
     * @return mixed
     */
    public static function init(array $configuration = [])
    {
        if (preg_match('#^/(?P<app>' . strtolower(str_replace('\\', '/', implode('|', $configuration['app']))) . ')/#', $_SERVER['REQUEST_URI'] . '/', $matches)) {
            $app = ucwords(str_replace('/', '\\', $matches['app']));
        } else {
            $app = array_pop($configuration['app']);
        }

        $class = sprintf('%s\App', $app);

        if (class_exists($class)) {
            $app = new $class;
        } elseif (class_exists($app)) {
            $app = new $app;
        }

        $configuration = array_replace_recursive($configuration, $app->getConfig());

        $services = new ServiceManager($configuration);
        $services->set('events', new Event\EventManager);
        $services->set('app', $app);

        $application = new static($services);

        // Prepare list of listeners to bootstrap
        $listeners = isset($configuration['listener']) ? $configuration['listener'] : [];

        return $application->bootstrap($listeners);
    }

    /**
     * @param array $listeners
     * @return $this
     */
    public function bootstrap(array $listeners = [])
    {
        $services = $this->services;
        $events =  $this->events;

        // Setup default listeners
        $listeners = array_unique(array_merge($this->listeners, $listeners));

        foreach ($listeners as $name => $listener) {
            $services->set($name, new $listener);
            $services->get($name)->attach($services, $events);
        }

        // Setup MVC Event
        $this->event = $event = new MvcEvent();
        $event->setName('bootstrap');
        $event->setTarget($this);
        $event->setServiceManager($services);

        // Trigger bootstrap events
        $events->triggerEvent($event);

        return $this;
    }

    /**
     * @return $this
     */
    public function run()
    {
        $events = $this->events;
        $event = $this->event;

        // Trigger route event
        $event->setName('route');
        $event->stopPropagation(false); // Clear before triggering
        $events->triggerEvent($event);

        // Trigger dispatch event
        $event->setName('dispatch');
        $event->stopPropagation(false); // Clear before triggering
        $events->triggerEvent($event);

        // Trigger render event
        $event->setName('render');
        $event->stopPropagation(false); // Clear before triggering
        $events->triggerEvent($event);

        // Trigger finish event
        $event->setName('finish');
        $event->stopPropagation(false); // Clear before triggering
        $events->triggerEvent($event);

        return $this;
    }
}
