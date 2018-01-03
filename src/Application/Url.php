<?php

namespace Ions\Mvc\Application;

/**
 * Class Url
 * @package Ions\Mvc\Application
 */
class Url
{
    /**
     * @var mixed
     */
    private $base;

    /**
     * Url constructor.
     * @param array $options
     */
    public function __construct(array $options)
    {
        $this->base = $options['base'];
        $this->path = $options['path'];
    }

    /**
     * @param $route
     * @param null $args
     * @param bool $secure
     * @return string
     */
    public function link($route, $args = null, $secure = false)
    {
        if ($secure) {
            $url = $this->base . $this->path . $route;
        } else {
            $url = $this->base . $this->path . $route;
        }

        if ($args) {
            if (is_array($args)) {
                $url .= '?' . http_build_query($args);
            } else {
                $url .= str_replace('&', '&amp;', '?' . ltrim($args, '&'));
            }
        }

        return $url;
    }

    /**
     * @param $route
     * @param null $args
     * @param bool $secure
     * @return string
     */
    public function base($route, $args = null, $secure = false)
    {
        if ($secure) {
            $url = $this->base . $route;
        } else {
            $url = $this->base . $route;
        }

        if ($args) {
            if (is_array($args)) {
                $url .= '?' . http_build_query($args);
            } else {
                $url .= str_replace('&', '&amp;', '?' . ltrim($args, '&'));
            }
        }

        return $url;
    }
}
