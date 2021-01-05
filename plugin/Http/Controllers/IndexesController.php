<?php

namespace Shemi\MeiliPress\Http\Controllers;

use Shemi\MeiliPress\Index;
use Shemi\MeiliPress\Query\PostsQuery;
use Shemi\MeiliPress\Sync\CronSyncJob;

class IndexesController extends Controller
{

	public function all()
	{

	}

	/**
	 * @return Index
	 */
	protected function getIndex()
	{
		$indexId = $this->request->get('index');

		if(! $indexId) {
			$this->error(__("Missing index id", MP_TD));
		}

		$index = Index::find($indexId);

		if(! $index) {
			$this->error(sprintf(__('Index with the id "%1$s" not exists.', MP_TD), $indexId));
		}

		return $index;
	}

	public function search()
	{
		$index = $this->getIndex();
		$term = $this->request->get('term');

		if(! $term) {
			$this->error(__("Missing search term", MP_TD));
		}

		$res = $index->getMsIndex()->search($term);

		return [
			'hits_count' => $res['nbHits'],
			'time' => $res['processingTimeMs'],
			'hits' => array_map(function($hit) use ($index) {
				$return = [];

				foreach ($index->fields()->onlyDisplay() as $field) {
					$value = $hit[$field->indexName];

					if($field->type === 'array' && is_array($value)) {
						$value = implode(', ', $value);
					}

					$return[$field->indexName] = (string) $value;
				}

				return $return;
			}, $res['hits'])
		];
	}

	public function state()
	{
		$index = $this->getIndex();
		$syncJob = $this->plugin->syncJob($index);

		return [
			'type' => $syncJob instanceof CronSyncJob ? 'cron' : 'ajax',
			'state' => $syncJob->state(),
			'indexName' => $index->name
		];
	}

	public function reindex()
	{
		$index = $this->getIndex();
		$syncJob = $this->plugin->syncJob($index);

		if($syncJob instanceof CronSyncJob) {
			$syncJob->backgroundRun();

			return [
				'content' => __("The sync will start in the background", MP_TD)
			];
		}

		return $syncJob->reindex();
	}

}