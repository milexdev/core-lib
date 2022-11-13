<?php

namespace Milex\CoreBundle\Event;

use Symfony\Component\EventDispatcher\Event;

/**
 * Class BuildJsEvent.
 */
class BuildJsEvent extends Event
{
    /**
     * @var string
     */
    protected $js = '';

    /**
     * @var bool
     */
    protected $debugMode;

    /**
     * @param bool $debugMode
     */
    public function __construct($js, $debugMode = false)
    {
        $this->js        = $js;
        $this->debugMode = $debugMode;
    }

    /**
     * @return string
     */
    public function getJs()
    {
        return $this->debugMode ? $this->js : \JSMin::minify($this->js);
    }

    /**
     * Append JS.
     *
     * @param string $js
     * @param string $section The section name. Shows when in debug mode
     *
     * @return $this
     */
    public function appendJs($js, $section = '')
    {
        if ($section && $this->debugMode) {
            $slashes = str_repeat('/', strlen($section) + 10);
            $this->js .= <<<JS
\n
{$slashes}
// {$section} Start
{$slashes}
\n
JS;
        }

        $this->js .= $js;

        if ($section && $this->debugMode) {
            $this->js .= <<<JS
\n
{$slashes}
// {$section} End
{$slashes}
JS;
        }

        return $this;
    }
}
