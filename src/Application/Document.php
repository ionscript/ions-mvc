<?php

namespace Ions\Mvc\Application;

/**
 * Class Document
 * @package Ions\Mvc\Application
 */
class Document
{
    /**
     * @var
     */
    private $title;
    /**
     * @var
     */
    private $description;
    /**
     * @var
     */
    private $keywords;
    /**
     * @var array
     */
    private $links = [];
    /**
     * @var array
     */
    private $styles = [];
    /**
     * @var array
     */
    private $scripts = [];

    /**
     * @param $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param $keywords
     */
    public function setKeywords($keywords)
    {
        $this->keywords = $keywords;
    }

    /**
     * @return mixed
     */
    public function getKeywords()
    {
        return $this->keywords;
    }

    /**
     * @param $href
     * @param $rel
     */
    public function addLink($href, $rel)
    {
        $this->links[$href] = [
            'href' => $href,
            'rel' => $rel
        ];
    }

    /**
     * @return array
     */
    public function getLinks()
    {
        return $this->links;
    }

    /**
     * @param $href
     * @param string $rel
     * @param string $media
     */
    public function addStyle($href, $rel = 'stylesheet', $media = 'screen')
    {
        $this->styles[$href] = [
            'href' => $href,
            'rel' => $rel,
            'media' => $media
        ];
    }

    /**
     * @return array
     */
    public function getStyles()
    {
        return $this->styles;
    }

    /**
     * @param $href
     * @param string $position
     */
    public function addScript($href, $position = 'header')
    {
        $this->scripts[$position][$href] = $href;
    }

    /**
     * @param string $position
     * @return array|mixed
     */
    public function getScripts($position = 'header')
    {
        if (isset($this->scripts[$position])) {
            return $this->scripts[$position];
        }
            return [];
    }
}
