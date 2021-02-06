<?php

namespace Shemi\MeiliPress;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use MeiliSearch\Client;
use Shemi\Core\Foundation\Plugin;
use Shemi\MeiliPress\Options\SettingsOptions;
use Shemi\MeiliPress\Sync\AjaxSyncJob;
use Shemi\MeiliPress\Sync\CronSyncJob;

if (!defined('ABSPATH')) {
	exit;
}

class MeiliPress extends Plugin
{

	protected $_client;

	protected $shareVariables = [];

	/**
	 * @param null $key
	 * @param null $default
	 * @return mixed|SettingsOptions
	 */
	public function settings($key = null, $default = null)
	{
		$settings = SettingsOptions::instance();

		return $key ? $settings->get($key, $default) : $settings;
	}

	public function share($key, $value = null)
	{
		if(! is_array($key)) {
			$key = ["{$key}" => $value];
		}

		foreach ($key as $k => $v) {
			Arr::set($this->shareVariables, $k, $v);
		}
	}

	public function getSharedVariables()
	{
		return $this->shareVariables;
	}

	public function makeClient($url, $masterKey)
	{
		if(! $url) {
			return;
		}

		return new Client($url, $masterKey);
	}

	public function client()
	{
		if($this->_client) {
			return $this->_client;
		}

		$this->_client = $this->makeClient(
			$this->settings('instance.url'),
			$this->settings('instance.masterKey')
		);

		return $this->_client;
	}

	public function isAdminActive()
	{
		$currentScreen = get_current_screen();

		return $currentScreen && Str::contains($currentScreen->id, 'meilipress');
	}

	/**
	 * @return MessagesServiceProvider
	 */
	public function messages()
	{
		return $this->provider('messages');
	}

	public function disabled()
	{
		if($this->settings()->isNew()) {
			return true;
		}

		try {
            $client = $this->client();
        } catch (\Exception $exception) {
		    return false;
        }

		if(! $client) {
			return true;
		}

		try {
            $client->health();
		}

		catch (\Exception $exception) {
			return true;
		}

		return false;
	}

	/**
	 * @param Index $index
	 * @return AjaxSyncJob|CronSyncJob
	 */
	public function syncJob(Index $index)
	{
		if($this->settings('sync.type') === 'ajax') {
			return new AjaxSyncJob($this, $index);
		}

		return new CronSyncJob($this, $index);
	}

	public function indexes()
	{
		return Index::all();
	}

	public function indexTypes()
	{
		return apply_filters('meilipress/index/types', [
			Index::class
		]);
	}

	public function indexPublicSearchSettings($indexName)
	{
		$index = Index::findByName($indexName);

		if(! $index) {
			throw new \Exception(sprintf(__('Index with the name "%1$s" not exists.', MP_TD), $indexName));
		}

		$name = $index->nameWithPrefix();
		$url = $this->settings('instance.url');
		$enabled = true;
		$message = "";
		$publicKey = "";

		try {
			$keys = $this->client()->getKeys();
			$publicKey = $keys['public'];
		} catch (\Exception $exception) {
			$enabled = false;
			$message = sprintf(__('MeiliPress unable get public key (%1$s)', MP_TD), $exception->getMessage());
		}

		return [
			'name' => $name,
			'key' => $publicKey,
			'url' => $url,
			'enabled' => $enabled,
			'message' => $message
		];
	}

}