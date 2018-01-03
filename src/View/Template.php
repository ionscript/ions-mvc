<?php

namespace Ions\Mvc\View;

/**
 * Class Template
 * @package Ions\Mvc\View
 */
abstract class Template implements TemplateInterface
{
    /**
     * @var string
     */
    protected $template = '';
    /**
     * @var array
     */
    protected $data = [];

    /**
     * @param array $data
     * @param bool $overwrite
     * @return $this
     */
    public function setData(array $data, $overwrite = false)
    {
        if ($overwrite) {
            $this->data = $data;
            return $this;
        }

        foreach ($data as $key => $value) {
            $this->data[(string) $key] = $value;
        }

        return $this;
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @return $this
     */
    public function clearData()
    {
        $this->data = [];
        return $this;
    }

    /**
     * @param $template
     * @return $this
     */
    public function setTemplate($template)
    {
        $this->template = (string) $template;
        return $this;
    }

    /**
     * @return string
     */
    public function getTemplate()
    {
        return $this->template;
    }
}
