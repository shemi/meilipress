<?php

namespace Shemi\MeiliPress\Fields;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Shemi\MeiliPress\Contracts\ToView;
use Shemi\MeiliPress\Documents\Document;

abstract class Field implements Arrayable, ToView
{
	public $id;

	public $fieldName = '';

	public $component = 'mp-field';

	public $localName = '';

	public $type = 'string';

	public $indexName = '';

	public $description = '';

	public $distinct = false;

	public $searchable = false;

	public $displayable = false;

	public $facetingable = false;

	public $removable = true;

	abstract public function getValue($resource = null);

	abstract protected function settings();

	public static function make($indexName = '', $localName = '')
	{
		$inst = new static;

		if($indexName) {
			$inst->indexName($indexName);
		}

		if($localName) {
			$inst->localName($localName);
		}

		return $inst;
	}

	public function localName($name)
	{
		$this->localName = $name;

		return $this;
	}

	public function type($type)
	{
		$this->type = $type;

		return $this;
	}

	public function indexName($name)
	{
		$this->indexName = $name;

		return $this;
	}

	public function description($description)
	{
		$this->description = $description;

		return $this;
	}

	public function setDistinct()
	{
		$this->distinct = true;

		return $this;
	}

	public function facetingable()
	{
		$this->facetingable = true;

		return $this;
	}

	public function searchable()
	{
		$this->searchable = true;

		return $this;
	}

	public function displayable()
	{
		$this->displayable = true;

		return $this;
	}

	public function load($rawField)
	{
		$this->id = Arr::get($rawField, 'id', '');
		$this->localName = Arr::get($rawField, 'localName', '');
		$this->indexName = Arr::get($rawField, 'indexName', '');
		$this->type = Arr::get($rawField, 'type', '');
		$this->description = Arr::get($rawField, 'description', '');
		$this->distinct = Arr::get($rawField, 'distinct', false);
		$this->searchable = Arr::get($rawField, 'searchable', false);
		$this->displayable = Arr::get($rawField, 'displayable', false);
		$this->removable = Arr::get($rawField, 'removable', true);
		$this->facetingable = Arr::get($rawField, 'facetingable', true);
	}

	public function toArray()
	{
		return [
			'class' => static::class,
			'id' => $this->id ?: Str::uuid()->serialize(),
			'localName' => $this->localName,
			'type' => $this->type,
			'indexName' => $this->indexName,
			'description' => $this->description,
			'distinct' => $this->distinct,
			'searchable' => $this->searchable,
			'displayable' => $this->displayable,
			'facetingable' => $this->facetingable
		];
	}

	public static function build($data)
	{
		if(! isset($data['class'])) {
			return null;
		}

		$class = $data['class'];

		if(! class_exists($class)) {
			throw new \Exception(sprintf(__('Field class with the name "%1$s" not exists.', MP_TD), $class));
		}

		/** @var Field $inst */
		$inst = new $class;

		if(! ($inst instanceof static)) {
			throw new \Exception(sprintf(__('The class "%1$s" must be instance of "%2$s"', MP_TD), $class, static::class));
		}

		$inst->load($data);

		return $inst;
	}

	public function toView()
	{
		return [
			'form' => $this->toArray(),
			'settings' => array_merge($this->settings(), [
				'component' => $this->component,
				'types' => [
					'string', 'int',
					'float', 'bool',
					'date', 'array',
					'object'
				],
				'fieldName' => $this->fieldName ?: Str::snake(class_basename(static::class), ' '),
				'removable' => $this->removable
			])
		];
	}

	protected function cast($value, $type = 'string')
	{
		switch ($type) {
			case 'string':
				return (string) wp_strip_all_tags($value, true);
			case 'int':
				return (int) $value;
			case 'float':
				return (float) $value;
			case 'bool':
				return (boolean) $value;
			case 'date':
				return $this->castDate($value);
			case 'array':
				return (array) $value;
			case 'object':
				return (object) $value;
		}

		return $value ? $value : null;
	}

	protected function castDate($value)
	{
		if($value instanceof \DateTime) {
			$value = $value->getTimestamp();
		}

		if(is_string($value)) {
			$value = (new \DateTime($value, get_option('timezone_string')))->getTimestamp();
		}

		return $value ? (int) $value : null;
	}

}