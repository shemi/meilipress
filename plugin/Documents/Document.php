<?php

namespace Shemi\MeiliPress\Documents;

use Illuminate\Contracts\Support\Arrayable;
use Shemi\MeiliPress\Fields\Field;
use Shemi\MeiliPress\Index;

abstract class Document implements Arrayable
{

	protected static $mark = "_meilipress_synced";

	protected $index;

	protected $resource;

	public function __construct($resource, Index $index)
	{
		$this->index = $index;
		$this->setResource($resource);
	}

	public function setResource($resource)
	{
		$this->resource = $resource;
	}

	abstract public function mark();

	abstract public static function removeMark($id);

	public function toArray()
	{
		$fields = $this->index->fields();
		$attributes = [];

		/** @var Field $field */
		foreach ($fields as $field) {
			$attributes[$field->indexName] = $field->getValue($this->resource);
		}

		return $attributes;
	}

}