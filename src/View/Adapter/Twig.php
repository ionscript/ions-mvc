<?php

namespace Ions\Mvc\View\Adapter;

use Ions\Mvc\View\Template;

/**
 * Class Twig
 * @package Ions\Mvc\View\Adapter
 */
final class Twig extends Template
{
    /**
     * @param $template
     * @param array $data
     * @return string
     * @throws \Exception
     */
    public function render($template , array $data = [])
    {
        $loader = new \Twig_Loader_Filesystem(dirname($template));
        $twig = new \Twig_Environment($loader);
        $data = array_merge($this->data, $data);

        if (is_file($template)) {

            try {
                ob_start();
                echo $twig->render(basename($template), $data);
                $output = ob_get_clean();
            } catch (\Exception $ex) {
                ob_end_clean();
                throw $ex;
            }

            return $output;
        }

        trigger_error('Error: Could not load template ' . $template . '!');
        exit();
    }
}
