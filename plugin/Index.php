<?php

namespace Shemi\MeiliPress;

use Carbon\Carbon;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use MeiliSearch\Endpoints\Indexes;
use Shemi\MeiliPress\Contracts\ToView;
use Shemi\MeiliPress\Documents\PostDocument;
use Shemi\MeiliPress\Fields\Field;
use Shemi\MeiliPress\Fields\FieldsCollection;
use Shemi\MeiliPress\Options\IndexesOptions;
use Shemi\MeiliPress\Query\PostsQuery;

class Index implements Arrayable, ToView
{
	public $id = '';

	public $name = '';

	public $status = 'enabled';

	public $createdAt;

	public $updatedAt;

	/**
	 * @var array $data
	 */
	protected $data = [];

	/**
	 * @var MeiliPress $plugin
	 */
	protected $plugin;

	/**
	 * @var Indexes $msIndex
	 */
	protected $msIndex;

	public $queryType = PostsQuery::class;

	public $documentType = PostDocument::class;

	public $reindexRequired = true;

	public $lastIndex = null;

	protected $fiiling = false;

	public function __construct($data = null)
	{
		$this->plugin = MeiliPress::instance();

		if(! empty($data)) {
			$this->fill($data);
		}
	}

	public function getMsIndex()
	{
		if(! $this->name) {
			throw new \Exception(__("The index is missing name.", MP_TD));
		}

		if($this->msIndex) {
			return $this->msIndex;
		}

		$this->msIndex = $this->plugin->client()->getIndex($this->nameWithPrefix());

		return $this->msIndex;
	}

	public function nameWithPrefix()
	{
		$prefix = $this->plugin->settings('instance.indexPrefix');

		return $prefix ? "{$prefix}{$this->name}" : $this->name;
	}

	public function fill($data)
	{
		$this->fiiling = true;

		$this->name = Arr::get($data, 'name');
		$this->id = Arr::get($data, 'id');
		$this->createdAt = Arr::get($data, 'created_at');
		$this->updatedAt = Arr::get($data, 'updated_at');
		$this->reindexRequired = Arr::get($data, 'reindex_required');
		$this->lastIndex = Arr::get($data, 'last_index');

		$data = Arr::get($data, 'data');
		$this->query(Arr::get($data, 'query'));
		$this->fields(Arr::get($data, 'fields'));
		$this->stopWords(Arr::get($data, 'stop_words'));
		$this->rankingRules(Arr::get($data, 'ranking_rules'));
		$this->synonyms(Arr::get($data, 'synonyms'));

		$this->fiiling = false;

		return $this;
	}

	public function id($id = null)
	{
		if(! $id) {
			return $this->id;
		}

		$this->id = $id;

		return $this;
	}

	public function name($name = null)
	{
		if(! $name) {
			return $this->name;
		}

		$this->name = $name;

		return $this;
	}

	public function status($status = null)
	{
		if(! $status) {
			return $this->status;
		}

		$this->status = $status;

		return $this;
	}

	/**
	 * @param null $timestamp
	 * @return $this|Carbon|null
	 */
	public function createdAt($timestamp = null)
	{
		if(! $timestamp) {
			return $this->createdAt ? mp_date($this->createdAt) : null;
		}

		$this->createdAt = $timestamp;

		return $this;
	}

	/**
	 * @param null $timestamp
	 * @return $this|null|Carbon
	 */
	public function updatedAt($timestamp = null)
	{
		if(! $timestamp) {
			return $this->updatedAt ? mp_date($this->updatedAt) : null;
		}

		$this->updatedAt = $timestamp;

		return $this;
	}

	public function data($key = null, $value = null)
	{
		if(is_null($key)) {
			return $this->data;
		}

		if(is_array($key)) {
			$this->data = $key;

			return $this;
		}

		if(is_null($value)) {
			return Arr::get($this->data, $key);
		}

		Arr::set($this->data, $key, $value);

		return $this;
	}

	public function query($data = null)
	{
		if(is_null($data)) {
			return $this->data('query');
		}

		if(! $this->fiiling) {
			$old = md5(json_encode($this->data('query')));
			$new = md5(json_encode($data));

			if(! $this->reindexRequired && $old !== $new) {
				$this->reindexRequired = true;
			}
		}

		return $this->data('query', $data);
	}

	/**
	 * @param array|null $data
	 * @return $this|FieldsCollection
	 *
	 * @throws \Exception
	 */
	public function fields(array $data = null)
	{
		if(is_null($data)) {
			return $this->data('fields');
		}

		$reindexRequired = false;
		$oldFields = $this->data('fields') ?: FieldsCollection::make([]);
		$rawFields = $data;
		$fields = FieldsCollection::make([]);

		foreach ($rawFields as $field) {
			if($field instanceof Field) {
				$fields->push($field);
				continue;
			}

			$fieldData = isset($field['form']) ? $field['form'] : $field;
			$field = Field::build($fieldData);
			$oldField = $oldFields->firstWhere('id', $field->id);

			if(! $field) {
				continue;
			}

			$fields->push($field);

			if(! $this->fiiling) {
				if(! $oldField ||
					$oldField->indexName !== $field->indexName ||
					$oldField->localName !== $field->localName ||
					$oldField->type !== $field->type) {
					$reindexRequired = true;
				}
			}
		}

		if(! $this->reindexRequired && $reindexRequired) {
			$this->reindexRequired = true;
		}

		return $this->data('fields', $fields);
	}

	public function synonyms($data = null)
	{
		if(is_null($data)) {
			return $this->data('synonyms') ?: [];
		}

		return $this->data('synonyms', $data);
	}

	public function stopWords($data = null)
	{
		if(is_null($data)) {
			return $this->data('stop_words') ?: [];
		}

		$data = array_values(array_filter(array_map('trim', $data)));

		return $this->data('stop_words', $data);
	}

	public function rankingRules($data = null)
	{
		if(! $data) {
			return $this->data('ranking_rules');
		}

		$data = array_values(array_filter(array_map('trim', $data)));

		return $this->data('ranking_rules', $data);
	}

	public function exists()
	{
		return !! $this->id();
	}

	/**
	 * @param $id
	 * @return static|null
	 */
	public static function find($id)
	{
		$data = IndexesOptions::instance()->get("indexes.{$id}");

		if(! $data) {
			return null;
		}

		return static::create($data);
	}

	/**
	 * @return Collection
	 */
	public static function all()
	{
		$rawIndexes = array_values(IndexesOptions::instance()->get("indexes", []));

		return Collection::make($rawIndexes)
			->map(function($rawIndex) {
				$class = isset($rawIndex['type']) ? $rawIndex['type'] : static::class;

				return (new $class)->fill($rawIndex);
			});
	}

	/**
	 * @param $name
	 * @return static|null
	 */
	public static function findByName($name)
	{
		return static::all()->first(function(Index $index) use ($name) {
			return $index->name() === $name;
		});
	}

	public static function create($data = null)
	{
		$inst = new static;

		if(is_array($data)) {
			$inst->fill($data);
		}

		else {
			$inst->data(static::defaultData());
		}

		return $inst;
	}

	public static function defaultData()
	{
		return apply_filters('meilipress/index/default_data', [
			'name' => '',
			'status' => 'enabled',
			'query' => PostsQuery::defaultParams(),
			'fields' => PostDocument::defaultFields(),
			'synonyms' => (array) [],
			'stop_words' => (array) [],
			'ranking_rules' => [
				"typo", "words", "proximity",
				"attribute", "wordsPosition", "exactness"
			],
			'reindex_required' => true,
			'last_index' => null
		]);
	}

	public function save()
	{
		if(! $this->createdAt) {
			$this->createdAt = time();
		}

		if(! $this->id) {
			$this->id = (string) Str::uuid();
		}

		$this->updatedAt = time();

		if(! IndexesOptions::instance()->setAndSave('indexes.'.$this->id, $this->toArray())) {
			throw new \Exception(__("Unable to save settings.", MP_TD));
		}

		$this->createOrUpdateMsIndex();

		return true;
	}

	public function createOrUpdateMsIndex()
	{
		$this->msIndex = $this->plugin->client()->getOrCreateIndex($this->nameWithPrefix());
		$this->updateMsFields();
		$this->updateMsSynonyms();
		$this->updateMsStopWords();
		$this->updateMsRankingRules();
	}

	public function sync(Collection $documents)
	{
		$this->msIndex = $this->plugin->client()->getIndex($this->nameWithPrefix());

		$this->msIndex->addDocuments($documents->toArray());

		$documents->each->mark();
	}

	public function updateMsFields()
	{
		$oldDistinct = $this->msIndex->getDistinctAttribute();
		$oldFaceting = $this->msIndex->getAttributesForFaceting();
		$oldSearchable = $this->msIndex->getSearchableAttributes();
		$oldDisplayed = $this->msIndex->getDisplayedAttributes();

		$distinct = '';
		$faceting = [];
		$searchable = [];
		$displayed = [];

		/** @var Field $field */
		foreach ($this->fields() as $field) {
			if(! $field->indexName) {
				continue;
			}

			if($field->distinct) {
				$distinct = $field->indexName;
			}

			if($field->searchable) {
				$searchable[] = $field->indexName;
			}

			if($field->displayable) {
				$displayed[] = $field->indexName;
			}

			if($field->facetingable) {
				$faceting[] = $field->indexName;
			}
		}

		if($distinct !== $oldDistinct) {
			$this->msIndex->updateDistinctAttribute($distinct);
		}

		if(! mp_array_equal_and_same_order($searchable, $oldSearchable)) {
			$this->msIndex->updateSearchableAttributes($searchable);
		}

		if(! mp_array_equal($displayed, $oldDisplayed)) {
			$this->msIndex->updateDisplayedAttributes($displayed);
		}

		if(! mp_array_equal($faceting, $oldFaceting)) {
			$this->msIndex->updateAttributesForFaceting($faceting);
		}
	}

	public function updateMsRankingRules()
	{
		$oldRankingRules = $this->msIndex->getRankingRules();
		$rankingRules = $this->rankingRules();

		if(! mp_array_equal_and_same_order($rankingRules, $oldRankingRules)) {
			$this->msIndex->updateRankingRules($rankingRules);
		}
	}

	public function updateMsSynonyms()
	{
		$oldSynonyms = $this->msIndex->getSynonyms();
		$synonyms = [];

		foreach ($this->synonyms() as $synonym) {
			$synonyms[$synonym['synonym']] = $synonym['words'];
		}

		$oldKeys = array_keys($oldSynonyms);
		$newKeys = array_keys($synonyms);

		$oldValues = Arr::flatten(array_values($oldSynonyms));
		$newValues = Arr::flatten(array_values($synonyms));

		if(! mp_array_equal($newKeys, $oldKeys) || ! mp_array_equal($newValues, $oldValues)) {
			$this->msIndex->updateSynonyms($synonyms);
		}
	}

	public function updateMsStopWords()
	{
		$oldStopWords = $this->msIndex->getStopWords();
		$stopWords = $this->stopWords();

		if(! mp_array_equal($stopWords, $oldStopWords)) {
			$this->msIndex->updateStopWords($stopWords);
		}
	}

	public function delete()
	{
		if(! $this->exists()) {
			return false;
		}

		$saved = IndexesOptions::instance()->deleteAndSave('indexes.'.$this->id);

		$msIndex = $this->getMsIndex();

		try {
			$msIndex->show();
		} catch (\Exception $exception) {
			$msIndex = null;
		}

		if(! $msIndex) {
			return $saved;
		}

		try {
			$msIndex->delete();
		} catch (\Exception $exception) {
			return false;
		}

		return $saved;
	}

	public function deleteAllDocuments()
	{
		try {
			$msIndex = $this->getMsIndex();
			$documents = $msIndex->getDocuments();
			$idField = $this->fields()->first(function(Field $field) {
				return $field->distinct;
			});

			if(! ($idField instanceof Field)) {
				$msIndex->deleteAllDocuments();

				return false;
			}

			foreach ($documents as $document) {
				$id = isset($document[$idField->indexName]) ? $document[$idField->indexName] : null;

				if($id) {
					$this->documentType::removeMark($id);
				}
			}

			$msIndex->deleteAllDocuments();
		} catch (\Exception $exception) {
			return false;
		}

		return true;
	}

	public function toArray()
	{
		return [
			'type' => static::class,
			'id' => $this->id,
			'name' => $this->name,
			'created_at' => $this->createdAt,
			'updated_at' => $this->updatedAt,
			'query_type' => $this->queryType,
			'document_type' => $this->documentType,
			'reindex_required' => $this->reindexRequired,
			'last_index' => $this->lastIndex,
			'data' => [
				'fields' => $this->fields()->toArray(),
				'query' => $this->query(),
				'stop_words' => $this->stopWords(),
				'synonyms' => $this->synonyms(),
				'ranking_rules' => $this->rankingRules()
			]
		];
	}

	public function toView()
	{
		return [
			'id' => $this->id,
			'name' => $this->name,
			'created_at' => mp_date_format($this->createdAt(), true),
			'updated_at' => mp_date_format($this->updatedAt(), true),
			'last_index' => mp_date_format($this->lastIndex, true),
			'reindex_required' => $this->reindexRequired,
			'index_stats' => $this->indexState(),
			'posts_count' => $this->postCount(),
			'sync_state' => $this->status,
			'data' => [
				'fields' => $this->fields()->toView(),
				'query' => $this->query(),
				'stop_words' => $this->stopWords(),
				'synonyms' => $this->synonyms(),
				'ranking_rules' => $this->rankingRules()
			]
		];
	}

	public function editUrl()
	{
		return MeiliPress::instance()->getPageUrl('meilipress-index', [
			'id' => $this->id
		]);
	}

	public function postCount()
	{
		$args = $this->query();

		if(! $this->exists()) {
			return 0;
		}

		return $this->queryType::count($args);
	}

	public function indexState($key = null)
	{
		$state = $this->lastIndex ? $this->getMsIndex()->stats() : null;

		return $state ? ($key ? Arr::get($state, $key) : $state) : null;
	}

	public function deleteDocument($postId)
	{
		$this->getMsIndex()->deleteDocument($postId);
		$this->documentType::removeMark($postId);
	}

	public function maybeDeleteDocument($postId)
	{
		$post = get_post($postId);
		$validPostType = empty($postTypes) ? true : in_array($post->post_type, $postTypes);
		$isMarked = $this->documentType::isMarked($post->ID);

		if($validPostType && $isMarked) {
			$this->deleteDocument($post->ID);
		}
	}

	public function maybeUpdatePostDocument($postId)
	{
		$post = get_post($postId);
		$postTypes = isset($this->query()['post_type']) ? $this->query()['post_type'] : [];
		$postStatuses = isset($this->query()['post_status']) ? $this->query()['post_status'] : [];

		$validPostType = empty($postTypes) ? true : in_array($post->post_type, $postTypes);
		$validPostStatus = empty($postStatuses) ? true : in_array($post->post_status, $postStatuses);

		if(! $validPostType) {
			return;
		}

		$isMarked = $this->documentType::isMarked($post->ID);

		if(! $validPostStatus && $isMarked) {
			$this->deleteDocument($post->ID);

			return;
		}

		if($this->postsNeedSync([$post->ID])) {
			$this->plugin->syncJob($this)->sync([$post]);
		}
		elseif ($isMarked) {
			$this->deleteDocument($post->ID);
		}
	}

	public function postsNeedSync($postIds, $one = true)
	{
		$postIds = array_values(array_filter(
			array_map(function($postId) {
				return is_int($postId) ? $postId : get_post($postId)->ID;
			}, (array) $postIds)
		));

		if(empty($postIds)) {
			return false;
		}

		$needSync = $this->queryType::test($this->query(), $postIds);

		if(count($postIds) === 1 && $one) {
			return in_array($postIds[0], $needSync);
		}

		return $needSync;
	}

	public static function description()
	{
		return __("Wordpress default index", MP_TD);
	}

}