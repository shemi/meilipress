<?php

namespace Shemi\MeiliPress\Fields;

class PostIdField extends PostField
{

	public function __construct()
	{
		parent::__construct();

		$this->fieldName = __("Post ID", MP_TD);
		$this->component = 'mp-index-post-field';
		$this->localName = 'ID';
		$this->indexName = 'id';
		$this->distinct = true;
		$this->removable = false;
		$this->searchable = false;
		$this->displayable = true;
	}

	/**
	 * @param \WP_Post $resource
	 */
	public function getValue($resource = null)
	{
		return (int) $resource->ID;
	}

	public static function postProperties()
	{
		$properties = parent::postProperties();
		$properties['ID'] = 'int';

		return $properties;
	}

}