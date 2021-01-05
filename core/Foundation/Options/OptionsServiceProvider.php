<?php

namespace Shemi\Core\Foundation\Options;

use Illuminate\Support\Collection;
use Shemi\Core\Support\ServiceProvider;

class OptionsServiceProvider extends ServiceProvider
{

	/**
	 * @var Collection $buckets
	 */
	protected $buckets;

	public function register()
	{
		$this->buckets = Collection::make([]);

		$this->registerBuckets();
	}

	protected function registerBuckets()
	{
		do_action('sp/options/register', $this);

		foreach ($this->plugin->config('plugin.options') as $optionBucket) {
			$this->registerBucket($optionBucket);
		}

		do_action('sp/options/registered', $this);
	}

	public function registerBucket($optionBucket)
	{
		$optionBucket = new $optionBucket($this->plugin);

		if(! ($optionBucket instanceof OptionBucket)) {
			throw new \Exception("The option bucket must be instance of ".OptionBucket::class);
		}

		$optionBucket->boot();

		$this->buckets->push($optionBucket);

	}



}