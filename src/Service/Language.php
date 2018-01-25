<?php

namespace Ions\Mvc\Service;

/**
 * Class Language
 * @package Ions\Mvc\Service
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
     * @return void
     */
    public function setLanguage($language)
    {
        $this->language = $language;
    }

    /**
     * @return string
     */
    public function getLanguage()
    {
        return $this->language;
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
     * @return string
     */
    public function getDirectory()
    {
        return $this->directory;
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
