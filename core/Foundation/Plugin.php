<?php

namespace Shemi\Core\Foundation;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Shemi\Core\Console\ConsoleServiceProvider;
use Shemi\Core\Container\Container;
use Shemi\Core\Contracts\Foundation\Plugin as PluginContract;
use Shemi\Core\Foundation\Http\HttpServiceProvider;
use Shemi\Core\Foundation\Http\Request;
use Shemi\Core\Foundation\Options\OptionsServiceProvider;
use Shemi\Core\Foundation\Pages\PagesServiceProvider;
use Shemi\Core\View\Environment as ViewEnvironment;

if (!defined('ABSPATH')) {
    exit;
}

abstract class Plugin extends Container implements PluginContract
{

    /**
     * The current globally available container (if any).
     *
     * @var static
     */
    protected static $instance;

    public static $hookNamespace;

    /**
     * The slug of this plugin.
     *
     * @var string
     */
    public $slug = '';

    /**
     * Buld in __FILE__ relative plugin.
     *
     * @var string
     */
    protected $file;

    /**
     * The base path for the plugin installation.
     *
     * @var string
     */
    protected $basePath;

    /**
     * The base uri for the plugin installation.
     *
     * @var string
     */
    protected $baseUri;
    /**
     * Internal use where store the plugin data.
     *
     * @var array
     */
    protected $pluginData = [];
    /**
     * A key value pairs array with the list of providers.
     *
     * @var array
     */
    protected $provides = [];

    protected $_config = [];

    protected $_request = null;

	/**
	 * @var ViewEnvironment
	 */
    protected $_viewEnv;

    public function __construct($basePath, $nameSpace = "")
    {
        $this->basePath = rtrim($basePath, '\/');

        $this->boot();
    }

    public function boot()
    {
        $this->file = $this->basePath . '/index.php';
        $this->baseUri = rtrim(plugin_dir_url($this->file), '\/');

        if (! function_exists('get_plugin_data')) {
            require_once(ABSPATH.'wp-admin/includes/plugin.php');
        }

        $this->pluginData = get_plugin_data($this->file, false);
        $this->slug = str_replace('-', '_', sanitize_title(basename($this->basePath)));

        $this->loadConfig();

        register_activation_hook($this->file, [$this, 'activation']);
        register_deactivation_hook($this->file, [$this, 'deactivation']);

        add_action('init', [$this, 'init']);

        static::$instance = $this;

        return $this;
    }

    public function request()
    {
        if (! $this->_request) {
            $this->_request = new Request();
        }

        return $this->_request;
    }

    protected function pluginBasename()
    {
        return plugin_basename($this->file);
    }

	public function basePath($path = "")
	{
		$path = ltrim($path, DIRECTORY_SEPARATOR);

		return "{$this->basePath}/{$path}";
	}

	public function baseUri($path = "")
	{
		$path = ltrim($path, DIRECTORY_SEPARATOR);

		return "{$this->baseUri}/{$path}";
	}

	public function publicPath($path = "")
    {
    	$path = ltrim($path, DIRECTORY_SEPARATOR);

        return $this->basePath("public/{$path}");
    }

    public function publicUri($path)
	{
		$path = ltrim($path, DIRECTORY_SEPARATOR);

		return $this->baseUri("public/{$path}");
	}

    public function loadConfig()
	{
		$path = $this->basePath('config');
		$files = scandir($path);

		foreach ($files as $filename) {
			if(Str::endsWith($filename, '.php')) {
				$key = str_replace('.php', '', $filename);
				$this->_config[$key] = include $path.DIRECTORY_SEPARATOR.$filename;
			}
		}
	}

    public function config($key = null, $default = null)
    {
        return Arr::get($this->_config, $key, $default);
    }

    public function pluginNamespace($name = "")
	{
		$class = new \ReflectionClass(static::class);

		return $class->getNamespaceName().'\\'.$name;
	}

	public function controllersNamespace($controller = "")
	{
		return $this->pluginNamespace("Http\\Controllers\\{$controller}");
	}

    public function activation()
    {
    	$activationFile = $this->basePath('plugin/activation.php');

        if(file_exists($activationFile)) {
        	include_once $activationFile;
		}
    }

    public function deactivation()
    {
    	$deactivationFile = $this->basePath('/plugin/deactivation.php');

    	if(file_exists($deactivationFile)) {
			include_once $deactivationFile;
		}
    }

	public function provider($name)
	{
		if (in_array($name, array_keys($this->provides))) {
			return $this->provides[$name];
		}

		return null;
	}

    public function init()
    {
    	$providers = $this->config('plugin.providers');

    	$defaultProviders = [
			'options' => OptionsServiceProvider::class,
    		'console' => ConsoleServiceProvider::class,
    		'pages' => PagesServiceProvider::class,
			'http' => HttpServiceProvider::class
		];

    	foreach (($defaultProviders + $providers) as $key => $provider) {
			$key = is_int($key) ? $provider : $key;
			$object = new $provider($this);

			$this->provides[$key] = $object;
		}

    	foreach ($this->provides as $providerName => $provider) {
			$provider->register();
		}

		foreach ($this->config('plugin.pages') as $pageClass) {
			$this->provider('pages')->addPage($pageClass);
		}
    }

    public function isAjax()
    {
        return  $this->request()->isAjax();
    }

	public function getPageUrl($pageSlug, $args = [])
	{
		return add_query_arg(
			array_merge(['page' => $pageSlug], $args),
			admin_url('admin.php')
		);
	}

	public function setupViewEnv()
	{
		if($this->_viewEnv) {
			return;
		}

		$this->_viewEnv = new ViewEnvironment($this->basePath("resources/views"), '.php');
		$this->_viewEnv->plugin = $this;
		$this->_viewEnv->request = $this->request();
		$this->_viewEnv->user = wp_get_current_user();
	}

	public function shareWithView($data)
	{
		$this->setupViewEnv();

		foreach ($data as $key => $value) {
			$this->_viewEnv->{$key} = $value;
		}
	}

	public function view($fileName, $data = [])
	{
		$this->setupViewEnv();

		return $this->_viewEnv->render($fileName, $data);
	}

	public function pluginData($key)
	{
		return Arr::get($this->pluginData, $key);
	}

	public function version()
	{
		return $this->pluginData('Version');
	}

	public static function instance()
	{
		return static::$instance;
	}

}