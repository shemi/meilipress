<?php

namespace Shemi\MeiliPress\Options;

use Shemi\Core\Foundation\Options\OptionBucket;

class IndexesOptions extends OptionBucket
{

	protected $name = "meilipress_indexes";

	protected $autoload = false;

	public function defaultOptions()
	{
		return [
			'indexes' => []
		];
	}

}