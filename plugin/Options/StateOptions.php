<?php

namespace Shemi\MeiliPress\Options;

use Illuminate\Support\Str;
use Shemi\Core\Foundation\Options\OptionBucket;

class StateOptions extends OptionBucket
{

	protected $name = "meilipress_state";

	protected $autoload = true;

	public function defaultOptions()
	{
		return [
			'last_test' => null,
			'sync_state' => []
		];
	}

	public function syncStates()
	{
		return $this->get('sync_state');
	}

	public function syncState($indexId)
	{
		return $this->get("sync_state.{$indexId}");
	}

	public function addSyncState($indexId, $current, $total)
	{
		$state = [
			'total' => (int) $total,
			'current' => (int) $current,
		];

		$this->setAndSave("sync_state.{$indexId}", $state);

		return $state;
	}

	public function updateSyncStateTotal($indexId, $total)
	{
		$state = $this->syncState($indexId);

		if(! $state) {
			return false;
		}

		$state['total'] = (int) $total;
		$this->setAndSave("sync_state.{$indexId}.total", (int) $total);

		return $state;
	}

	public function updateSyncState($indexId, $current)
	{
		$state = $this->get("sync_state.{$indexId}");

		if(! $state) {
			return false;
		}

		if($current >= $state['total']) {
			$this->removeSyncState($indexId);
		}
		else {
			$this->setAndSave("sync_state.{$indexId}.current", (int) $current);
		}

		$state['current'] = (int) $current;

		return $state;
	}

	public function removeSyncState($indexId)
	{
		$this->deleteAndSave("sync_state.{$indexId}");
	}

}