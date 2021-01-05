<?php

namespace Shemi\Core\Foundation\Http;

use Shemi\Core\Foundation\Pages\Page;
use Shemi\Core\Foundation\Plugin;

if (! defined('ABSPATH')) {
    exit;
}

abstract class Controller
{

    protected $request;

    protected $pageSlug;

    protected $_page;

    protected $plugin;

    public $noPrivActions = [];

    public function __construct(Plugin $plugin)
	{
		$this->plugin = $plugin;
		$this->request = $plugin->request();
	}

	/**
	 * @return Page|null
	 */
	protected function page()
	{
		if($this->pageSlug && ! $this->_page) {
			$this->_page = $this->plugin
				->provider('pages')
				->getPage($this->pageSlug);
		}

		return $this->_page;
	}

	protected function verifyNonce($action)
	{
		if(! $this->request->verifyNonce($action)) {
			$this->error(__("Invalid nonce. try refreshing the page", MP_TD));
		}

		return true;
	}

	/**
	 * Redirect the browser to a localtion. If the header has been sent, then a Javascript and meta refresh will
	 * inserted into the page.
	 *
	 * @param string $location
	 * @param array $extraData
	 */
    protected function redirect(string $location = '', array $extraData = [])
    {
        $args = array_filter(array_keys($_REQUEST), function ($e) {
            return ($e !== 'page');
        });

        if (empty($location)) {
            $location = remove_query_arg($args);
        }

        $this->success(array_merge([
			'redirect' => $location
		], $extraData));
    }

    protected function success($data = [], $statusCode = 200)
	{
		if(empty($data) && $statusCode === 200) {
			$statusCode = 204;
		}

		wp_send_json_success($data, $statusCode);
	}

	protected function validationErrors($errors = [])
	{
		wp_send_json_error($errors, 422);
	}

	protected function error($message, $action = [], $statusCode = 500)
	{
		if(is_array($message)) {
			$body = $message;
		}

		else {
			$body = ['content' => $message];

			if(! empty($action)) {
				$body['action'] = $action;
			}
		}

		wp_send_json_error($body, $statusCode);
	}

}