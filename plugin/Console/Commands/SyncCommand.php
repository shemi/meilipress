<?php

namespace Shemi\MeiliPress\Console\Commands;

use Shemi\Core\Console\Commands\Command;
use Shemi\MeiliPress\Index;
use Shemi\MeiliPress\Sync\CronSyncJob;

class SyncCommand extends Command
{

	public static $signature = "meilipress:sync {index=all}";

	public function run()
	{
		$index = $this->get('index');
		$indexes = Index::all();

		if($index !== 'all') {
			$names = array_map('trim', explode(',', $index));
			$indexes = $indexes->filter(function(Index $index) use ($names) {
				return in_array($index->name(), $names);
			});
		}

		foreach ($indexes as $index) {
			$this->line(sprintf(__('Start syncing %1$s', MP_TD), $index->name()));

			$sync = new CronSyncJob($this->plugin, $index);
			$state = $sync->run();

			$this->line(sprintf(__('Sync %1$s/%2$s', MP_TD), (string) $state['current'], (string) $state['total']));

			while ($state['total'] > $state['current']) {
				$state = $sync->run();
			}

			$this->line(sprintf(__('Finish syncing %1$s', MP_TD), $index->name()));
		}
	}

	public static function shortDescription()
	{
		return "";
	}

	public static function description()
	{
		return "";
	}

}