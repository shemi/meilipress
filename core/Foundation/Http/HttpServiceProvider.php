<?php

namespace Shemi\Core\Foundation\Http;

use Illuminate\Support\Str;
use PHPMailer\PHPMailer\Exception;
use Shemi\Core\Support\ServiceProvider;

class HttpServiceProvider extends ServiceProvider
{

	protected $actions = [];

	public function register()
	{
		$this->registerControllers();
	}

	public function registerControllers()
	{
		$controllers = $this->plugin->config('plugin.controllers');

		foreach ($controllers as $ctrl) {
			$class = new \ReflectionClass($ctrl);
			$methods = $class->getMethods(\ReflectionMethod::IS_PUBLIC);
			$controllerName = Str::snake(str_replace('Controller', '', class_basename($class->name)), '/');
			$pluginSlug = $this->plugin->slug;
			$action = "{$pluginSlug}/{$controllerName}";
			/** @var Controller $controller */
			$controller = new $ctrl($this->plugin);

			foreach ($methods as $method) {
				if(Str::startsWith($method->name, '__')) {
					continue;
				}

				$methodAction = "{$action}/{$method->name}";

				add_action("wp_ajax_{$methodAction}", [$this, 'call']);

				if(in_array($method, $controller->noPrivActions)) {
					add_action("wp_ajax_nopriv_{$methodAction}", [$this, 'callNoPriv']);
				}

				$this->actions[$methodAction] = [
					'controller' => $controller,
					'method' => $method->name
				];

				$this->plugin->share('security.'.$methodAction, wp_create_nonce($methodAction));
			}

		}
	}

	public function call($ajaxAction = null)
	{
		$ajaxAction = $ajaxAction ?: $this->plugin->request()->get('action');

		if(! isset($this->actions[$ajaxAction])) {
			throw new Exception("unknown nopriv action.");
		}

		/** @var Controller $controller */
		$controller = $this->actions[$ajaxAction]['controller'];
		$method = $this->actions[$ajaxAction]['method'];
		$response = "";

		try {
			$response = $controller->{$method}();
		}

		catch (\Exception $exception) {
			wp_send_json_error([
				'content' => $exception->getMessage(),
			], 500);
		}

		if(empty($response)) {
			wp_send_json_success([], 204);
		}

		if(is_string($response)) {
			$response = ['content' => $response];
		}

		wp_send_json_success($response);
	}

	public function callNoPriv()
	{
		$ajaxAction = $this->plugin->request()->get('action');

		if(! isset($this->actions[$ajaxAction])) {
			throw new Exception("unknown nopriv action.");
		}

		$action = $this->actions[$ajaxAction];

		if(! in_array($action['method'], $action['controller']->noPrivActions)) {
			throw new Exception("action require priv.");
		}

		return $this->call($ajaxAction);
	}


}