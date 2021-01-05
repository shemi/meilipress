<?php

namespace Shemi\Core\Foundation\Options;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Shemi\Core\Foundation\Plugin;

abstract class OptionBucket
{

	/**
	 * @var static $instance
	 */
	protected static $instances = [];

	protected $name;

	protected $autoload = false;

	protected $loaded = false;

	protected $lastModifiedBy = null;

	protected $modifiedAt = null;

	protected $createdAt = null;

	protected $_bucket = [];

	/**
	 * @var Plugin $_plugin
	 */
	protected $_plugin;

	public function __construct(Plugin $plugin)
	{
		$this->_plugin = $plugin;
	}

	public function name()
	{
		return $this->name;
	}

	public function isAutoload()
	{
		return $this->autoload;
	}

	public function boot()
	{
		static::$instances[class_basename(static::class)] = $this;

		do_action("sp/options/{$this->name()}/boot", $this);

		if($this->isAutoload()) {
			$this->load();
		}
	}

	public function load()
	{
		$options = apply_filters(
			"sp/options/{$this->name()}/load",
			get_option($this->name(), ['data' => $this->defaultOptions()]),
			$this
		);

		$this->setOptions($options['data']);
		$this->createdAt = $options['createdAt'] ?? null;
		$this->modifiedAt = $options['modifiedAt'] ?? null;
		$this->lastModifiedBy = $options['lastModifiedBy'] ?? null;

		$this->loaded = true;

		do_action("sp/options/{$this->name()}/loaded", $this);
	}

	public function loadIfNotLoaded()
	{
		if(! $this->loaded) {
			$this->load();
		}
	}

	public function all()
	{
		$this->loadIfNotLoaded();

		return array_merge($this->defaultOptions(), $this->_bucket);
	}

	public function get($key, $default = null)
	{
		$this->loadIfNotLoaded();

		if(is_null($default)) {
			$default = Arr::get($this->defaultOptions(), $key);
		}

		return apply_filters(
			"sp/options/{$this->name()}/get/{$key}",
			Arr::get($this->_bucket, $key, $default),
			$this
		);
	}

	/**
	 * @param $key
	 * @param $value
	 * @param bool $save
	 * @return $this|bool
	 */
	public function set($key, $value = null, $save = false)
	{
		$this->loadIfNotLoaded();

		$value = apply_filters(
			"sp/options/{$this->name()}/set/{$key}",
			$value,
			$this
		);

		Arr::set($this->_bucket, $key, $value);

		if($save) {
			return $this->save();
		}

		return $this;
	}

	/**
	 * @param $key
	 * @param $value
	 * @return bool
	 */
	public function setAndSave($key, $value)
	{
		return $this->set($key, $value, true);
	}

	/**
	 * @param $key
	 * @param bool $save
	 * @return $this|bool
	 */
	public function delete($key, $save = false)
	{
		Arr::forget($this->_bucket, $key);

		do_action("sp/options/{$this->name()}/{$key}", $this);

		if($save) {
			return $this->save();
		}

		return $this;
	}

	/**
	 * @param $key
	 * @return bool
	 */
	public function deleteAndSave($key)
	{
		return $this->delete($key, true);
	}

	public function clear()
	{
		$this->_bucket = $this->defaultOptions();

		do_action("sp/options/{$this->name()}/clear/before", $this);

		delete_option($this->name());

		do_action("sp/options/{$this->name()}/clear/after", $this);

		return $this;
	}

	public function isNew()
	{
		return ! ((bool) $this->createdAt);
	}

	public function save()
	{
		do_action(
			"sp/options/{$this->name()}/save/before",
			$this
		);

		$data = apply_filters(
			"sp/options/{$this->name()}/save",
			$this->_bucket,
			$this
		);

		$toSave = [
			'lastModifiedBy' => get_current_user_id(),
			'modifiedAt' => time(),
			'createdAt' => $this->createdAt ?: time(),
			'data' => json_encode($data)
		];

		$saved = update_option($this->name(), $toSave, $this->isAutoload());

		do_action(
			"sp/options/{$this->name()}/save/after",
			$this
		);

		return $saved;
	}

	public function defaultOptions()
	{
		return [];
	}

	public function setOptions($options)
	{
		if(is_string($options)) {
			$options = json_decode($options, true);
		}

		$this->_bucket = (array) $options;

		return $this;
	}

	/**
	 * @return static
	 */
	public static function instance()
	{
		return static::$instances[class_basename(static::class)];
	}

}