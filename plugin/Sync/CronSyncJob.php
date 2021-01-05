<?php

namespace Shemi\MeiliPress\Sync;

use Shemi\MeiliPress\Options\StateOptions;

class CronSyncJob extends SyncJob
{

	public function backgroundRun()
	{
		StateOptions::instance()->set("background.{$this->index->id}", true);
	}

}