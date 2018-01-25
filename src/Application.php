<?php

namespace Ions\Mvc;

use Ions\Event;

class Application
{
    const ERROR_NOT_FOUND = 'error-not-found';

    /**
     * @var
     */
    protected $event;

    /**
     * @var mixed
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

    public function __construct(ServiceManager $serviceManager, Event\EventManagerInterface $events = null) {
        $this->services = $serviceManager;
        $this->setEventManager($events ?: $serviceManager->get('events'));
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
        $services->setService('events', new Event\EventManager);
        $services->setService('app', $app);

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
        $serviceManager = $this->services;
        $events         = $this->events;

        // Setup default listeners
        $listeners = array_unique(array_merge($this->listeners, $listeners));

        foreach ($listeners as $listener) {
            $serviceManager->get($listener)->attach($events);
        }

        // Setup MVC Event
        $this->event = $event  = new Action();
        $event->setName(Action::EVENT_BOOTSTRAP);
        $event->setTarget($this);

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
        $event->setName(Action::EVENT_ROUTE);
        $event->stopPropagation(false); // Clear before triggering
        $events->triggerEvent($event);

        // Trigger dispatch event
        $event->setName(Action::EVENT_DISPATCH);
        $event->stopPropagation(false); // Clear before triggering
        $events->triggerEvent($event);

        // Trigger finish event
        $event->setName(Action::EVENT_FINISH);
        $event->stopPropagation(false); // Clear before triggering
        $events->triggerEvent($event);

        return $this;
    }

    public function getServiceManager()
    {
        return $this->services;
    }

    public function setEventManager(Event\EventManagerInterface $eventManager)
    {
        $this->events = $eventManager;
        return $this;
    }

    public function getEventManager()
    {
        return $this->events;
    }
}
