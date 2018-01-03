<?php

namespace Ions\Mvc\View;

/**
 * Interface TemplateInterface
 * @package Ions\Mvc\View
 */
interface TemplateInterface
{
    /**
     * @param array $variables
     * @return mixed
     */
    public function setData(array $variables);

    /**
     * @return mixed
     */
    public function getData();

    /**
     * @param $template
     * @return mixed
     */
    public function setTemplate($template);

    /**
     * @return mixed
     */
    public function getTemplate();
}
