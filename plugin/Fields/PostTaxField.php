<?php

namespace Shemi\MeiliPress\Fields;

use Illuminate\Support\Arr;

class PostTaxField extends Field
{
	protected $fields = 'names';

	public function __construct()
	{
		$this->fieldName = __("Post terms", MP_TD);
		$this->component = 'mp-index-post-tax-field';
		$this->type = 'array';
		$this->searchable = true;
		$this->displayable = true;
	}

	public function arguments($arguments)
	{
		$this->arguments = (array) $arguments;

		return $this;
	}

	/**
	 * @param \WP_Post $resource
	 */
	public function getValue($resource = null)
	{
		$terms = wp_get_object_terms($resource->ID, $this->localName, [
			'fields' => $this->fields
		]);

		if(is_wp_error($terms)) {
			return [];
		}

		return $this->cast($terms, 'array');
	}

	public function load($rawField)
	{
		parent::load($rawField);
		$this->fields = Arr::get($rawField, 'fields', 'names');
	}

	public function toArray()
	{
		return array_merge(parent::toArray(), [
			'fields' => $this->fields
		]);
	}

	protected function settings()
	{
		return [
			'fields' => [
				'ids' => __("Returns an array of term IDs (int[]).", MP_TD),
				'tt_ids' => __("Returns an array of term taxonomy IDs (int[]).", MP_TD),
				'names' => __("Returns an array of term names (string[]).", MP_TD),
				'slugs' => __("Returns an array of term slugs (string[]).", MP_TD)
			]
		];
	}
}