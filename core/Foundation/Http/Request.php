<?php

namespace Shemi\Core\Foundation\Http;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;

if (!defined('ABSPATH')) exit;

class Request implements \ArrayAccess
{

	protected $posts = [];

	protected $cookies = [];

	protected $fiels = [];

	public function __construct()
	{
		$this->posts = $this->loadPosts();
		$this->fiels = $_FILES;
		$this->cookies = $_COOKIE;
	}

	protected function loadPosts()
	{
		$request = $_REQUEST;
		$data = isset($request['data']) ? $request['data'] : null;

		if($data && is_string($data)) {
			$data = json_decode(wp_unslash(urldecode($data)), true);

			if(json_last_error() !== JSON_ERROR_NONE) {
				return $request;
			}
		}

		if($data && is_array($data)) {
			$request = array_merge($request, $data);
		}

		return $request;
	}

	public function getMethodAttribute()
	{
		if (isset($_POST['_method'])) {
			return strtolower($_POST['_method']);
		}

		return strtolower($_SERVER['REQUEST_METHOD']);
	}

	public function verifyNonce($nonce)
	{
		$security = $this->get('security');

		if(! $security) {
			return false;
		}


		return wp_verify_nonce($security, $nonce) >= 1;
	}

	public function get($key, $default = null)
	{
		return Arr::get($this->posts, $key, $default);
	}

	public function cookie($key, $default = null)
	{
		return Arr::get($this->cookies, $key, $default);
	}

	public function ip()
	{
		if (isset($_SERVER["HTTP_CF_CONNECTING_IP"])) {
			$_SERVER['REMOTE_ADDR'] = $_SERVER["HTTP_CF_CONNECTING_IP"];
			$_SERVER['HTTP_CLIENT_IP'] = $_SERVER["HTTP_CF_CONNECTING_IP"];
		}

		$client  = $_SERVER['HTTP_CLIENT_IP'] ?? '';
		$forward = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? '';
		$remote  = $_SERVER['REMOTE_ADDR'] ?? '';

		if(filter_var($client, FILTER_VALIDATE_IP))
		{
			return $client;
		}

		elseif(filter_var($forward, FILTER_VALIDATE_IP))
		{
			return $forward;
		}

		return $remote;
	}

	public function all()
	{
		return $this->posts;
	}

	public function isAjax()
	{
		if (
			defined('DOING_AJAX') ||
			(isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
			strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest')
		) {
			return true;
		}

		return false;
	}

	public static function isVerb($verb)
	{
		$verb = strtolower($verb);

		return ($verb == strtolower($_SERVER['REQUEST_METHOD']));
	}

	public function __get($name)
	{
		$method = Str::studly("get_{$name}_attribute");

		if (method_exists($this, $method)) {
			return $this->{$method}();
		}

		return null;
	}

	public function file($name)
	{
		return Arr::get($this->fiels, $name);
	}

	public function offsetExists($offset)
	{
		return Arr::has($this->posts, $offset);
	}

	public function offsetGet($offset)
	{
		return $this->get($offset);
	}

	public function offsetSet($offset, $value)
	{
		Arr::set($this->posts, $offset, $value);
	}

	public function offsetUnset($offset)
	{
		unset($this->posts[$offset]);
	}
}