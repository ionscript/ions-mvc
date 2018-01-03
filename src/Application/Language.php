<?php

namespace Ions\Mvc\Application;

/**
 * Class Language
 * @package Ions\Mvc\Application
 */
class Language
{
    /**
     * @var string
     */
    private $language = 'en-gb';
    /**
     * @var
     */
    private $directory;
    /**
     * @var array
     */
    private $data = [];

    /**
     * @param $language
     */
    public function setLanguage($language)
    {
        $this->language = $language;
    }

    /**
     * @param $directory
     * @return $this
     */
    public function setDirectory($directory)
    {
        $this->directory = $directory;
        return $this;
    }

    /**
     * @param null $key
     * @return array|mixed|null
     */
    public function get($key = null)
    {
        if($key === null) {
           return $this->data;
        }

        return (isset($this->data[$key]) ? $this->data[$key] : $key);
    }

    /**
     * @param string $route
     */
    public function load($route = '')
    {
        $_ = [];

        $file = $this->directory . '/' . $this->language .'/' . $this->language . '.php';

        if (is_file($file)) {
            include_once $file;
        }

        if($route) {
            $file = $this->directory . '/' . $this->language . '/' . $route . '.php';

            if (is_file($file)) {
                include_once $file;
            }
        }

        $this->data = array_merge($this->data, $_);
    }
}
