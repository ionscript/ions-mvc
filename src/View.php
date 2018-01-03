<?php

namespace Ions\Mvc;

use Ions\Mvc\View\TemplateInterface;

/**
 * Class View
 * @package Ions\Mvc
 */
class View
{
    /**
     * @var
     */
    private $adapter;

    /**
     * @var array
     */
    private static $adapters = [
        'phtml' => View\Adapter\Phtml::class,
        'twig' => View\Adapter\Twig::class
    ];

    /**
     * View constructor.
     * @param array $options
     */
    public function __construct(array $options = [])
    {
        $adapter = isset($options['type']) ? static::$adapters[$options['type']] : array_shift(static::$adapters);
        $this->adapter = new $adapter($options);
    }

    /**
     * @param TemplateInterface $adapter
     */
    public function setAdapter(TemplateInterface $adapter)
    {
        $this->adapter = $adapter;
    }

    /**
     * @return mixed
     */
    public function getAdapter()
    {
        return $this->adapter;
    }

    /**
     * @param $template
     * @param array $data
     * @return mixed
     */
    public function render($template, array $data = [])
    {
        $this->adapter->setTemplate($template);

        if ($data) {
            $this->adapter->setData($data);
        }

        return $this->adapter->render();
    }
}
