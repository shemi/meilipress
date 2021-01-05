<?php

namespace Shemi\MeiliPress\Fields;

class PostField extends Field
{

	public function __construct()
	{
		$this->fieldName = __("Post attribute", MP_TD);
		$this->component = 'mp-index-post-field';
		$this->searchable = false;
		$this->displayable = false;
	}

	public static function postProperties()
	{
		return [
			'post_author' => 'int',
			'post_date' => 'date',
			'post_date_gmt' => 'date',
			'post_content' => 'string',
			'post_title' => 'string',
			'post_excerpt' => 'string',
			'post_status' => 'string',
			'comment_status' => 'string',
			'ping_status' => 'string',
			'post_password' => 'string',
			'post_name' => 'string',
			'to_ping' => 'string',
			'pinged' => 'string',
			'post_modified' => 'date',
			'post_modified_gmt' => 'date',
			'post_parent' => 'int',
			'guid' => 'string',
			'menu_order' => 'string',
			'post_type' => 'string',
			'post_mime_type' => 'string',
			'comment_count' => 'int'
		];
	}

	/**
	 * @param \WP_Post $resource
	 */
	public function getValue($resource = null)
	{
		return $this->cast($resource->{$this->localName}, $this->type);
	}

	public function load($rawField)
	{
		$postProperties = static::postProperties();
		$type = isset($postProperties[$this->localName]) ? $postProperties[$this->localName] : 'string';

		parent::load($rawField);

		$this->type($type);
	}

	public function toArray()
	{
		$postProperties = static::postProperties();
		$this->type = isset($postProperties[$this->localName]) ? $postProperties[$this->localName] : 'string';

		return parent::toArray();
	}

	protected function settings()
	{
		return [
			'properties' => static::postProperties()
		];
	}
}