<?php

namespace Ions\Mvc\View\Adapter;

use \Ions\Mvc\View\Template;

/**
 * Class Phtml
 * @package Ions\Mvc\View\Adapter
 */
class Phtml extends Template
{
    /**
     * @var string
     */
    private $content = '';

    /**
     * @return $this
     */
    public function getEngine()
    {
        return $this;
    }

    /**
     * @return string
     * @throws \Exception
     * @throws \RuntimeException|\UnexpectedValueException
     */
    public function render()
    {
        extract($this->data, EXTR_OVERWRITE);

            if (! $this->template) {
                throw new \RuntimeException(sprintf(
                    '%s: Unable to render template "%s";',
                    __METHOD__,
                    $this->template
                ));
            }

            try {
                ob_start();
                $includeReturn = include $this->template;
                $this->content = ob_get_clean();
            } catch (\Exception $ex) {
                ob_end_clean();
                throw $ex;
            }
            if ($includeReturn === false && empty($this->content)) {
                throw new \UnexpectedValueException(sprintf(
                    '%s: Unable to render template "%s"; file include failed',
                    __METHOD__,
                    $this->template
                ));
            }

        return $this->content;
    }
}
