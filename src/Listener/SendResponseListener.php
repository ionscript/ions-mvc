<?php

namespace Ions\Mvc\Listener;

use Ions\Event\EventManagerInterface;
use Ions\Event\EventManager;
use Ions\Event\ListenerInterface;
use Ions\Event\ListenerTrait;
use Ions\Mvc\Action;
use Ions\Mvc\Response\SendResponseEvent;
use Ions\Mvc\Response\SendHttpResponse;
use Ions\Mvc\Response\SendPhpResponse;
use Ions\Mvc\Response\SendSimpleStreamResponse;
use Ions\Std\ResponseInterface as Response;

/**
 * Class SendResponseListener
 * @package Ions\Mvc\Listener
 */
class SendResponseListener implements ListenerInterface
{
    use ListenerTrait;

    /**
     * @var
     */
    protected $event;
    /**
     * @var
     */
    protected $events;

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
     * @throws \InvalidArgumentException
     */
    public function getEventManager()
    {
        if (!$this->events instanceof EventManagerInterface) {
            $this->setEventManager(new EventManager());
        }

        return $this->events;
    }

    /**
     * @param EventManagerInterface $events
     * @param int $priority
     * @return void
     */
    public function attach(EventManagerInterface $events, $priority = 1)
    {
        $this->listeners[] = $events->attach('finish', [$this, 'sendResponse'], -10000);
    }

    /**
     * @param Action $e
     * @return null
     * @throws \InvalidArgumentException
     */
    public function sendResponse(Action $e)
    {
        $services = $e->getTarget()->getServiceManager();
        $response = $services->get('response');
        $events = $this->getEventManager();

        if (!$response instanceof Response) {
            return null;
        }

        $event = $this->getEvent();
        $event->setResponse($response);
        $events->triggerEvent($event);
    }

    /**
     * @return SendResponseEvent
     * @throws \InvalidArgumentException
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

        $events->attach('sendResponse', new SendPhpResponse(), -1000);
        $events->attach('sendResponse', new SendSimpleStreamResponse(), -3000);
        $events->attach('sendResponse', new SendHttpResponse(), -4000);
    }
}
