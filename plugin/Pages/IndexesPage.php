<?php

namespace Shemi\MeiliPress\Pages;

use Shemi\Core\Foundation\Pages\Page;
use Shemi\Core\Foundation\Plugin;
use Shemi\MeiliPress\Index as IndexModel;

class IndexesPage extends Page
{

	public function __construct(Plugin $plugin)
	{
		$this->title = __("MeiliPress - Indexes", MP_TD);
		$this->menuTitle = __("Indexes", MP_TD);
		$this->slug = "meilipress-indexes";

		parent::__construct($plugin);
	}

	public function parent()
	{
		return "meilipress";
	}

	public function boot()
	{
		add_action('admin_enqueue_scripts', function() {
			$this->plugin()->share([
				'indexes_data' => [
					'indexes' => $this->getAllIndexes(),
					'columns' => $this->getTableColumns(),
					'actions' => $this->getTableActions(),
					'bulkActions' => $this->getTableBulkActions()
				]
			]);
		}, 1);
	}

	protected function getAllIndexes()
	{
		$dateFormat = get_option('date_format');
		$timeFormat = get_option('time_format');

		return IndexModel::all()
			->map(function(IndexModel $index) use ($dateFormat, $timeFormat) {
				return apply_filters('meilipress/indexes/table/rows', [
					'id' => $index->id(),
					'name' => $index->name(),
					'syncStatus' => $index->status() === 'enabled' ? __("Sync Active", MP_TD) : __("Sync Disabled", MP_TD),
					'indexState' => $index->indexState('isIndexing') ? __("Indexing...", MP_TD) : __("Idle", MP_TD),
					'postCount' => $index->postCount(),
					'documentsCount' => $index->indexState('numberOfDocuments'),
					'editUrl' => $index->editUrl(),
					'createdAt' => $index->createdAt()->format("{$dateFormat} {$timeFormat}"),
					'updatedAt' => $index->updatedAt()->format("{$dateFormat} {$timeFormat}"),
				], $index, $this);
			})
			->values();
	}

	protected function getTableColumns()
	{
		return apply_filters('meilipress/indexes/table/columns', [
			'name' => [
				'label' => __("Index name", MP_TD),
				'sortable' => true
			],
			'syncStatus' => [
				'label' => __("Sync state", MP_TD),
				'sortable' => true
			],
			'indexState' => [
				'label' => __("Index state", MP_TD),
				'sortable' => true
			],
			'postCount' => [
				'label' => __("Posts count", MP_TD),
				'sortable' => true
			],
			'documentsCount' => [
				'label' => __("Documents Count", MP_TD),
				'sortable' => true
			],
			'createdAt' => [
				'label' => __("Created at", MP_TD),
				'sortable' => true
			],
			'updatedAt' => [
				'label' => __("Updated at", MP_TD),
				'sortable' => true
			],
		], $this);
	}

	protected function getTableActions()
	{
		return apply_filters('meilipress/indexes/table/actions', [
			[
				'key' => 'edit',
				'label' => __("Edit", MP_TD)
			],
			[
				'key' => 'reindex',
				'label' => __("Reindex", MP_TD)
			],
			[
				'key' => 'trash',
				'label' => __("Delete", MP_TD)
			]
		], $this);
	}

	public function getTableBulkActions()
	{
		return apply_filters('meilipress/indexes/table/bulk_actions', [
			[
				'key' => 'trash',
				'label' => __("Delete", MP_TD)
			]
		], $this);
	}

	public function render()
	{
		echo $this->plugin()->view('admin.indexes', [
			'page' => $this,
			'plugin' => $this->plugin()
		]);
	}
}