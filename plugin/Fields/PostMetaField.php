<?php

namespace Shemi\MeiliPress\Fields;

use Illuminate\Support\Arr;

class PostMetaField extends Field
{

	public function __construct()
	{
		$this->fieldName = __("Post meta", MP_TD);
		$this->component = 'mp-index-post-meta-field';
		$this->searchable = true;
		$this->displayable = true;
	}

	public function type($type)
	{
		$this->type = $type;

		return $this;
	}

	/**
	 * @param \WP_Post $resource
	 */
	public function getValue($resource = null)
	{
		return $this->cast($resource->{$this->localName}, $this->type);
	}

	protected function settings()
	{
		return [

		];
	}
}