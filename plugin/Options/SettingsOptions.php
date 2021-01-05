<?php

namespace Shemi\MeiliPress\Options;

use Illuminate\Support\Str;
use Shemi\Core\Foundation\Options\OptionBucket;

class SettingsOptions extends OptionBucket
{

	protected $name = "meilipress_settings";

	protected $autoload = true;

	public function defaultOptions()
	{
		$prefix = Str::slug(parse_url(home_url(), PHP_URL_HOST), '_').'_';

		return [
			'instance' => [
				'url' => 'http://127.0.0.1:7700',
				'indexPrefix' => $prefix,
				'masterKey' => '',
			],
			'sync' => [
				'documents_per_sync' => 50,
				'type' => 'ajax'
			],
			'deactivation' => [
				'delete_all_on_deactivation' => false,
				'delete_meilisearch_indexes_on_deactivation' => false
			]
		];
	}

}