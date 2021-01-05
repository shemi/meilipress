<?php

namespace Shemi\Core\Console;

use Shemi\Core\Support\ServiceProvider;

class ConsoleServiceProvider extends ServiceProvider
{


	public function register()
	{
		if (class_exists('\WP_CLI')) {
			foreach ($this->plugin->config('plugin.commends') as $commend) {
				$description = $commend::description();

				\WP_CLI::add_command($commend::name(), function($args, $assocArgs) use ($commend) {
					$commend::boot($this->plugin, $args, $assocArgs)->run();
				}, [
					'shortdesc' => $commend::shortDescription() ?: $description,
					'longdesc' => $description
				]);
			}
		}
	}



}