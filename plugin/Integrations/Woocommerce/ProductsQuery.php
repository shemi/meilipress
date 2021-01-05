<?php

namespace Shemi\MeiliPress\Integrations\Woocommerce;

class ProductsQuery
{

	public static function defaultParams()
	{
		return [
			'post_type' => ['product'],
			'post_status' => ['publish']
		];
	}

	public static function taxQueryDefaultForm()
	{
		return [];
	}

	public static function dateQueryDefaultForm()
	{
		return [];
	}

	public static function metaQueryDefaultForm()
	{
		return [];
	}

	public static function postTypes()
	{
		return [
			[
				'label' => 'Products',
				'id' => 'product'
			]
		];
	}

	public static function postStatuses()
	{
		return [];
	}

	public static function postTypesTaxonomies($types = [])
	{
		$return = [];
		$taxonomies = get_object_taxonomies(['product'], 'objects');

		foreach ($taxonomies as $taxonomyId => $taxonomy) {
			$return[] = [
				'label' => $taxonomy->label,
				'id' => $taxonomy->name
			];
		}

		return $return;
	}

	public static function parseArgs(array $args)
	{
		$args = apply_filters("meilipress/query/products/parse/before", $args);

		return apply_filters("meilipress/query/products/parse", $args);
	}

	public static function count($args)
	{
		$args = static::parseArgs($args);
		$args['fields'] = 'ids';
		$args['limit'] = -1;

		$query = new \WC_Product_Query($args);

		return $query->post_count;
	}

	public static function getTotalBatches($args, $perBatch = 50)
	{
		$args = static::parseArgs($args);
		$args['posts_per_page'] = $perBatch;
		$args['fields'] = 'ids';
		$query = new \WP_Query($args);

		return $query->max_num_pages;
	}

	public static function batch($args, $perBatch = 50, $batch = 1)
	{
		$args = static::parseArgs($args);
		$args['posts_per_page'] = $perBatch;
		$args['paged'] = $batch;
		$query = new \WP_Query($args);

		return [
			'resources' => $query->posts,
			'totalBatches' => (int) $query->max_num_pages
		];
	}

	public static function test($args, $postIds)
	{
		$args = static::parseArgs($args);
		$args['posts_per_page'] = -1;
		$args['fields'] = 'ids';
		$args['post__in'] = (array) $postIds;
		$args['ignore_sticky_posts'] = true;
		$query = new \WP_Query($args);

		return $query->posts;
	}

	public static function getRelatedObjectIds($args, array $termIds, $taxonomy)
	{
		$args = static::parseArgs($args);

		if (isset($args['tax_query'])) {
			$args['tax_query'] = [
				'relation' => 'AND',
				$args['tax_query']
			];
		} else {
			$args['tax_query'] = [
				'relation' => 'AND'
			];
		}

		$args['tax_query'][] = [
			'taxonomy' => $taxonomy,
			'terms' => $termIds,
			'field' => 'term_id',
			'operator' => 'IN',
		];

		$args['fields'] = 'ids';
		$query = new \WP_Query($args);

		return $query->posts;
	}

}