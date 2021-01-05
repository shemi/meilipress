<?php

namespace Shemi\MeiliPress\Fields;

class PostFunctionField extends Field
{

	protected $arguments = [];

	public function __construct()
	{
		$this->fieldName = __("Post function", MP_TD);
		$this->component = 'mp-index-post-function-field';
		$this->type = 'string';
		$this->searchable = false;
		$this->displayable = false;
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
		$arguments = array_merge([$resource], $this->arguments);

		return call_user_func_array($this->localName, $arguments);
	}

	public function toArray()
	{
		return array_merge(
			parent::toArray(),
			[
				'arguments' => $this->arguments
			]
		);
	}

	protected function settings()
	{
		return [

		];
	}
}