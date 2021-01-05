<?php

namespace Shemi\MeiliPress\Http\Controllers;

use Shemi\MeiliPress\Pages\SettingsPage;

class SettingsController extends Controller
{

	protected $pageSlug = 'meilipress-settings';

	public function testConnection()
	{
		$this->verifyNonce("meilipress/settings/testConnection");

		if(! current_user_can($this->page()->capability())) {
			$this->error(__("You can't do this!", MP_TD));
		}

		$url = $this->request->get('url');
		$masterKey = $this->request->get('masterKey', '');

		$client = $this->plugin->makeClient($url, $masterKey);
		$version = $client->version();

		return [
			'content' => sprintf(__('Connected successfully to MeiliSearch (version: %1$s)', MP_TD), $version['pkgVersion'] ?? '')
		];
	}

	public function save()
	{
		$this->verifyNonce("meilipress/settings/save");

		if(! current_user_can($this->page()->capability())) {
			$this->error(__("You can't do this!", MP_TD));
		}

		$url = $this->request->get('instance.url');
		$masterKey = $this->request->get('instance.masterKey', '');
		$indexPrefix = $this->request->get('instance.indexPrefix');

		$documentsPerCycle = $this->request->get('sync.documents_per_sync');
		$syncType = $this->request->get('sync.type');

		if(! $url) {
			$this->validationErrors([
				'instance' => ['url' => __("The url field is required!", MP_TD)]
			]);
		}

		$settings = $this->plugin->settings();
		$settings->set('instance', compact('url', 'masterKey', 'indexPrefix'));
		$settings->set('sync', [
			'documents_per_sync' => $documentsPerCycle,
			'type' => $syncType
		]);
		$settings->save();

		$client = $this->plugin->makeClient($url, $masterKey);

		try  {
			$version = $client->version();
		} catch (\Exception $exception) {
			$this->error([
				'content' => __('Saved. unable to connect to MiliSearch. test your connection.', MP_TD),
			]);
		}

		$this->success([
			'content' => sprintf(__('Saved. MeiliSearch (version: %1$s) connected.', MP_TD), $version['pkgVersion'] ?? ''),
			'action' => [
				'label' => __("Create index", MP_TD),
				'url' => $this->plugin->getPageUrl('meilipress-index')
			]
		]);
	}

}