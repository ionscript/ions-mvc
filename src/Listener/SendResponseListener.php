<?php

namespace Ions\Mvc\Listener;

use Ions\Event\Listener;
use Ions\Event\EventManagerInterface;
use Ions\Event\EventManager;
use Ions\Mvc\MvcEvent;
use Ions\Mvc\Sender\SendResponseEvent;
use Ions\Mvc\Sender\HttpResponseSender;
use Ions\Mvc\Sender\PhpEnvironmentResponseSender;
use Ions\Mvc\Sender\SimpleStreamResponseSender;
use Ions\Mvc\ServiceManager;
use Ions\Std\ResponseInterface as Response;

/**
 * Class SendResponseListener
 * @package Ions\Mvc\Listener
 */
class SendResponseListener extends Listener
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
     * @var
     */
    protected $services;

    /**
     * @param EventManagerInterface $eventManager
     * @return $this
     */
    public function setEventManager(EventManagerInterface $eventManager)
    {
        $this->events = $eventManager;
        $this->attachDefaultListeners();
        return $this;
    }

    /**
     * @return EventManagerInterface
     */
    public function getEventManager()
    {
        if (!$this->events instanceof EventManagerInterface) {
            $this->setEventManager(new EventManager());
        }

        return $this->events;
    }

    /**
     * @param ServiceManager $serviceManager
     * @param EventManagerInterface $events
     * @param int $priority
     * @return void
     */
    public function attach(ServiceManager $serviceManager, EventManagerInterface $events, $priority = 1)
    {
        $this->services = $serviceManager;
        $this->listeners[] = $events->attach('finish', [$this, 'sendResponse'], -10000);
    }

    /**
     * @param MvcEvent $e
     * @return null
     */
    public function sendResponse(MvcEvent $e)
    {
        $response = $this->services->get('response');
        $events = $this->getEventManager();

        if (!$response instanceof Response) {
            return null;
        }

        $event = $this->getEvent();
        $event->setResponse($response);
        $event->setTarget($this);
        $events->triggerEvent($event);
    }

    /**
     * @return SendResponseEvent
     */
    public function getEvent()
    {
        if (!$this->event instanceof SendResponseEvent) {
            $this->setEvent(new SendResponseEvent());
        }
        return $this->event;
    }

    /**
     * @param SendResponseEvent $e
     * @return $this
     */
    public function setEvent(SendResponseEvent $e)
    {
        $this->event = $e;
        return $this;
    }

    /**
     * @return void
     */
    protected function attachDefaultListeners()
    {
        $events = $this->getEventManager();

        $events->attach('sendResponse', new PhpEnvironmentResponseSender(), -1000);
        $events->attach('sendResponse', new SimpleStreamResponseSender(), -3000);
        $events->attach('sendResponse', new HttpResponseSender(), -4000);
    }
}
