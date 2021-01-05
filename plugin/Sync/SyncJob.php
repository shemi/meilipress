<?php

namespace Shemi\MeiliPress\Sync;

use Illuminate\Support\Collection;
use Shemi\MeiliPress\Index;
use Shemi\MeiliPress\MeiliPress;
use Shemi\MeiliPress\Options\SettingsOptions;
use Shemi\MeiliPress\Options\StateOptions;

abstract class SyncJob
{

	/**
	 * @var MeiliPress $plugin
	 */
	protected MeiliPress $plugin;

	/**
	 * @var Index $index
	 */
	protected Index $index;

	public function __construct(MeiliPress $plugin, Index $index)
	{
		$this->plugin = $plugin;
		$this->index = $index;
	}

	public function state()
	{
		$state = StateOptions::instance()->syncState($this->index->id);

		if($state) {
			return $state;
		}

		$batchSize = SettingsOptions::instance()->get('sync.documents_per_sync');
		$total = $batch = $this->index->queryType::getTotalBatches($this->index->query(), $batchSize);

		return [
			'total' => $total,
			'current' => 0
		];
	}

	public function reindex()
	{
		$state = StateOptions::instance()->syncState($this->index->id);

		if(! $state) {
			$this->index->deleteAllDocuments();
		}

		return $this->run();
	}

	public function run()
	{
		$state = StateOptions::instance()->syncState($this->index->id);

		if(! $state) {
			$state = StateOptions::instance()
				->addSyncState($this->index->id, 0, 0);
		}

		$batchSize = SettingsOptions::instance()->get('sync.documents_per_sync');
		$batchNumber = $state['current'] + 1;

		/** @var \WP_Query $query */
		$batch = $this->index->queryType::batch($this->index->query(), $batchSize, $batchNumber);
		$total = $batch['totalBatches'];

		if($total !== $state['total']) {
			StateOptions::instance()->updateSyncStateTotal($this->index->id, $total);
		}

		$state = StateOptions::instance()->updateSyncState($this->index->id, $batchNumber);

		$this->sync($batch['resources']);

		if($state['current'] >= $state['total']) {
			$this->index->reindexRequired = false;
			$this->index->lastIndex = time();
			$this->index->save();
		}

		return $state;
	}

	public function sync($resources)
	{
		$documents = Collection::make((array) $resources)
			->map(function($resource) {
				return new $this->index->documentType($resource, $this->index);
			});

		$this->index->sync($documents);
	}



}