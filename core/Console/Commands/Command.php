<?php

namespace Shemi\Core\Console\Commands;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Shemi\Core\Foundation\Plugin;
use Shemi\MeiliPress\MeiliPress;

abstract class Command
{
	protected static $argumentsRegex = "/^\{([^\-\-\s]*)\}$/";

	protected static $optionsRegex = "/^\{\-\-([^\s]*)\}$/";

	public static $signature = "";

	public static $description = "";

	protected $_args = [];

	protected $name;

	/**
	 * @var Plugin
	 */
	protected $plugin;

	abstract public function run();

	public function __construct(Plugin $plugin, $args, $assocArgs)
	{
		$this->plugin = $plugin;
		$this->setArgs($args, $assocArgs);
	}

	public function setArgs($args, $assocArgs)
	{
		$this->_args = $assocArgs;

		foreach (static::availableArguments() as $index => $argument) {
			if(isset($args[$index])) {
				$this->_args[$argument['name']] = $args[$index];
			}
		}
	}

	public function validate()
	{
		foreach (static::availableArguments() as $argument) {
			if(! $argument['required']) {
				continue;
			}

			if(is_null($this->get($argument['name']))) {
				$this->error("The Argument \"{$argument['name']}\" is required!");
			}
		}

		return $this;
	}

	public function get($key)
	{
		$availableArguments = array_merge(static::availableArguments(), static::availableOptions());
		$argumentInfo = null;
		$defaultValue = null;

		foreach ($availableArguments as $argument) {
			if($argument['name'] === $key) {
				$argumentInfo = $argument;
				break;
			}
		}

		if(! $argumentInfo) {
			return Arr::get($this->_args, $key);
		}

		if($argumentInfo['option'] && ! $argumentInfo['expectValue'] && ! isset($this->_args[$key])) {
			return false;
		}

		elseif($argumentInfo['option'] && ! $argumentInfo['expectValue'] && isset($this->_args[$key])) {
			return true;
		}

		return Arr::get($this->_args, $key, $argumentInfo['default']);
	}

	public function warning($message)
	{
		\WP_CLI::warning($this->name(). ": " .$message);

		return $this;
	}

	public function line($message = "")
	{
		if(empty($message)) {
			\WP_CLI::line("");

			return $this;
		}

		\WP_CLI::line($this->name(). ": " .$message);

		return $this;
	}

	public function success($message)
	{
		\WP_CLI::success($this->name(). ": " .$message);

		return $this;
	}

	public function error($message, $exit = true)
	{
		\WP_CLI::error($this->name(). ": " .$message, $exit);

		return $this;
	}

	public static function name()
	{
		return explode(' ', static::$signature)[0] ?? '';
	}

	public static function shortDescription()
	{
		return static::$description;
	}

	public static function description()
	{
		return static::$description;
	}

	public static function boot(Plugin $plugin, array $args, array $assocArgs)
	{
		return (new static($plugin, $args, $assocArgs))
			->validate();
	}

	public static function availableArguments()
	{
		return static::parseSignature(static::$argumentsRegex);
	}

	public static function availableOptions()
	{
		return static::parseSignature(static::$optionsRegex, true);
	}

	protected static function parseSignature($regex, $options = false)
	{
		$parts = explode(' ', static::$signature);
		array_shift($parts);

		return array_values(
			array_filter(
				array_map(function($part) use ($regex, $options) {
					if(empty($part)) {
						return false;
					}

					$matches = [];

					preg_match_all($regex, $part, $matches, PREG_SET_ORDER, 0);

					$pattern = array_flatten($matches)[1] ?? null;

					if(! $pattern) {
						return false;
					}

					return static::parseAttribute($pattern, $options);
				}, $parts)
			)
		);
	}

	protected static function parseAttribute($pattern, $option = false)
	{
		if(empty($pattern)) {
			return false;
		}

		$parts = explode("=", $pattern, 2);
		$name = array_shift($parts);
		$default = array_pop($parts) ?: null;
		$required = ! $option;
		$expectValue = ! $option;

		if($option && ! Str::contains($pattern, '=')) {
			$expectValue = false;
			$default = true;
		}

		if($option && ! $default) {
			$default = true;
		}

		if(Str::endsWith($name, '?')) {
			$required = false;
			$name = str_replace('?', '', $name);
		}

		return compact('name', 'required', 'default', 'option', 'expectValue');
	}

}