<?php

namespace Shemi\MeiliPress\Documents;

use Illuminate\Support\Collection;
use Shemi\MeiliPress\Fields\FieldsCollection;
use Shemi\MeiliPress\Fields\PostFunctionField;
use Shemi\MeiliPress\Fields\PostIdField;
use Shemi\MeiliPress\Fields\PostField;
use Shemi\MeiliPress\Fields\PostMetaField;
use Shemi\MeiliPress\Fields\PostTaxField;

class PostDocument extends Document
{

	public function setResource($resource)
	{
		parent::setResource(get_post($resource));
	}

	public static function defaultFields()
	{
		return FieldsCollection::make([
			PostIdField::make('id')
				->description(__("The post ID", MP_TD)),
			PostField::make('title', 'post_title')
				->description(__("The post title", MP_TD))
				->searchable()
				->displayable(),
			PostField::make('content', 'post_content')
				->description(__("The post content", MP_TD))
				->searchable()
				->displayable(),
			PostField::make('date', 'post_date')
				->description(__("The post local publication time.", MP_TD))
				->displayable(),
			PostFunctionField::make('url', 'get_the_permalink')
				->description( __("The post permalink (URL)", MP_TD))
				->arguments([
					[
						'name' => 'leavename',
						'value' => '',
						'default' => 'false'
					]
				])
				->displayable(),
			PostFunctionField::make('image', 'get_the_post_thumbnail_url')
				->description( __("The post thumbnail url", MP_TD))
				->arguments([
					[
						'name' => 'size',
						'value' => 'post-thumbnail',
						'default' => 'post-thumbnail'
					]
				])
				->displayable()
		]);
	}

	public static function availableFields()
	{
		return FieldsCollection::make([
			PostField::make()->description(__("Post Field (WP_Post properties)", MP_TD)),
			PostFunctionField::make()->description( __("Custom post function: any function that takes the first argument WP_Post", MP_TD)),
			PostMetaField::make()->description(__("Post meta (custom field)", MP_TD)),
			PostTaxField::make()->description(__("Post taxonomy terms", MP_TD)),
		]);
	}

	public function mark()
	{
		update_post_meta($this->resource->ID, static::$mark, 1);
	}

	public static function removeMark($id)
	{
		delete_post_meta($id, static::$mark);
	}

	public static function isMarked($id)
	{
		return get_post_meta($id, static::$mark, true) == 1;
	}

}