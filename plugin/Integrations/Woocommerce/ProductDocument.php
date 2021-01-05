<?php

namespace Shemi\MeiliPress\Integrations\Woocommerce;

use Shemi\MeiliPress\Documents\Document;
use Shemi\MeiliPress\Fields\FieldsCollection;
use Shemi\MeiliPress\Fields\PostField;
use Shemi\MeiliPress\Fields\PostFunctionField;
use Shemi\MeiliPress\Fields\PostMetaField;
use Shemi\MeiliPress\Fields\PostTaxField;

class ProductDocument extends Document
{

	/**
	 * @var \WC_Product $product
	 */
	protected $resource;

	public function setResource($resource)
	{
		parent::setResource(wc_get_product($resource));
	}

	public function mark()
	{
		$this->resource->add_meta_data(static::$mark, 1);
		$this->resource->save();
	}

	public static function removeMark($id)
	{
		$product = wc_get_product($id);

		if($product) {
			$product->delete_meta_data(static::$mark);
			$product->save();
		}
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

}