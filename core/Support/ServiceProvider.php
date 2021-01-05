<?php

namespace Shemi\Core\Support;

use Shemi\Core\Foundation\Plugin;

abstract class ServiceProvider
{

    /**
     * Register the service provider.
     *
     * @return void
     */
    abstract public function register();

    /**
     * Instance of main plugin.
     *
     * @var Plugin $plugin
     */
    protected $plugin;

    public function __construct(Plugin $plugin)
    {
        $this->plugin = $plugin;
    }

    /**
     * Dynamically handle missing method calls.
     *
     * @param string $method
     * @param array  $parameters
     */
    public function __call(string $method, $parameters)
    {
        if ($method === 'boot') {
            return;
        }
    }
}