<?php

namespace Ions\Mvc;

/**
 * Class Service
 * @package Ions\Mvc
 */
class ServiceManager
{
    /**
     * @var array
     */
    protected static $services = [];

    /**
     * @var bool
     */
    private $configured = false;

    /**
     * @var bool
     */
    private $override = false;

    /**
     * ServiceManager constructor.
     * @param array $config
     * @throws \DomainException
     */
    public function __construct(array $config = [])
    {
        $this->configure($config);
    }

    /**
     * @param $key
     * @return mixed
     */
    public function __get($key)
    {
        return $this->get($key);
    }

    /**
     * @param $key
     * @param $value
     */
    public function __set($key, $value)
    {
        $this->setService($key, $value);
    }

    /**
     * @param $key
     * @return bool
     */
    public function __isset($key)
    {
        return $this->has($key);
    }

    /**
     * @param $name
     * @return mixed
     */
    public function get($name)
    {
        if (isset(static::$services[$name]) && is_object(static::$services[$name])) {
            return static::$services[$name];
        }

        return static::$services[$name] = $this->createService($name);
    }

    /**
     * @param $name
     * @param $service
     * @throws \DomainException
     */
    public function setService($name, $service)
    {
        $this->configure(['service' => [$name => $service]]);
    }

    /**
     * @param array $config
     * @return $this
     * @throws \DomainException
     */
    public function configure(array $config)
    {
        $this->validateOverrides($config);

        if (isset($config['service'])) {

            if (isset($config['config'])) {
                $config['service'] = array_merge_recursive(
                    $config['service'],
                    array_intersect_key($config['config'], $config['service'])
                );
            }

            static::$services = array_replace($config['service'], static::$services);
        }

        $this->configured = true;

        return $this;
    }

    /**
     * @param $name
     * @return bool
     */
    public function has($name)
    {
        return isset(static::$services[$name]);
    }

    /**
     * @param $flag
     */
    public function setOverride($flag)
    {
        $this->override = (bool)$flag;
    }

    /**
     * @return bool
     */
    public function getOverride()
    {
        return $this->override;
    }

    /**
     * @param $name
     * @return mixed
     * @throws \RuntimeException
     */
    private function createService($name)
    {
        try {
            $object = $this->getService($name);
        } catch (\Exception $exception) {
            throw new \RuntimeException(sprintf(
                'Service with name "%s" could not be created. Reason: %s',
                $name,
                $exception->getMessage()
            ), (int)$exception->getCode(), $exception);
        }

        return $object;
    }

    /**
     * @param $name
     * @return mixed
     * @throws \InvalidArgumentException
     */
    private function getService($name)
    {
        $service = isset(static::$services[$name]) ? static::$services[$name] : $name;

        if (is_string($service) && class_exists($service)) {
            $service = new $service();
        }

        if (is_array($service)) {
            if (isset($service[0])) {
                $class = array_shift($service);

                if (method_exists($class, 'create')) {
                    $service = $class::create($service);
                } else {
                    $service = new $class($service);
                }
            } else {
                $service = (object)$service;
            }
        }

        if (is_object($service)) {
            static::$services[$name] = $service;

            return $service;
        }

        throw new \InvalidArgumentException(sprintf(
            'Unable to resolve service "%s"; '
            . 'you provided it during configuration?',
            $name
        ));
    }

    /**
     * @param array $services
     * @throws \DomainException
     */
    private function validateOverride(array $services)
    {
        $detected = [];

        foreach ($services as $service) {
            if (isset(static::$services[$service])) {
                $detected[] = $service;
            }
        }

        if (empty($detected)) {
            return;
        }

        throw new \DomainException(sprintf(
            'An updated/new service is not allowed, as the container does not allow '
            . 'changes for services with existing instances; the following '
            . 'already exist in the container: %s',
            implode(', ', $detected)
        ));
    }

    /**
     * @param array $config
     * @throws \DomainException
     */
    private function validateOverrides(array $config)
    {
        if ($this->override || !$this->configured) {
            return;
        }

        if (isset($config['service'])) {
            $this->validateOverride(array_keys($config['service']));
        }
    }
}
