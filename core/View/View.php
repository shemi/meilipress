<?php

namespace Shemi\Core\View;

use Shemi\Core\Foundation\Plugin;

if (! defined('ABSPATH')) {
    exit;
}

class View
{

    /**
     * A plugin instance container.
     *
     * @var $container
     */
    protected $container;

    protected $key;

    protected $data;

    /**
     * Create a new View.
     *
     * @param Plugin $container Usually a container/plugin.
     * @param null  $key       Optional. This is the path of view.
     * @param null  $data      Optional. Any data to pass to view.
     */
    public function __construct($container, $key = null, $data = null)
    {
        $this->container = $container;
        $this->key       = $key;
        $this->data      = $data;
    }

    /**
     * Get the filename.
     *
     * @return string
     */
    protected function filename()
    {
        $filename = str_replace('.', DIRECTORY_SEPARATOR, $this->key) . '.php';

        return $filename;
    }

    /**
     * Get the string rappresentation of a view.
     *
     * @return string
     */
    public function __toString(): string
    {
        return (string) $this->render();
    }

    /**
     * Return the content of view.
     *
     * @return string
     */
    public function toHTML(): string
    {
        return $this->render(true);
    }

    /**
     * Get the view content.
     *
     * @param bool $asHTML Set to TRUE to get the content of view as string/html.
     * @return mixed
     */
    public function render($asHTML = false)
    {
        $viewIncludeCallback = function () {
            $plugin = $this->container;

            if (! is_null($this->data) && is_array($this->data)) {
                foreach ($this->data as $var) {
                    extract($var);
                }
            }

            include $plugin->basePath("/resources/views/{$this->filename()}");
        };

        if ($this->container->isAjax() || $asHTML) {
            ob_start();
			$viewIncludeCallback();
            $content = ob_get_contents();
            ob_end_clean();

            return $content;
        }

        return $viewIncludeCallback();
    }

    /**
     * Data to pass to the view.
     *
     * @param mixed $data Array or single data.
     *
     * @example     $instance->with( 'foo', 'bar' )
     * @example     $instance->with( [ 'foo' => 'bar' ] )
     *
     * @return $this
     */
    public function with($data)
    {
        if (is_array($data)) {
            $this->data[] = $data;
        } elseif (func_num_args() > 1) {
            $this->data[] = [$data => func_get_arg(1)];
        }

        return $this;
    }

}