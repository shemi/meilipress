<?php

namespace Shemi\MeiliPress\Query;

use Illuminate\Support\Arr;

class PostsQuery
{

	public static function defaultParams()
	{
		return [
			'post_type' => [],
			'post_status' => ['publish'],
			'author' => '',
			'author_name' => '',
			'post_parent' => '',
			'date_query' => [
				'relation' => 'AND',
				'column' => 'post_date',
				'queries' => (array) []
			],
			'tax_query' => [
				'relation' => 'AND',
				'queries' => (array) []
			],
			'meta_query' => [
				'relation' => 'AND',
				'queries' => (array) []
			]
		];
	}

	public static function taxQueryDefaultForm()
	{
		return [
			'taxonomy' => '',
			'terms' => '',
			'field' => 'term_id',
			'operator' => 'IN',
			'include_children' => true,
		];
	}

	public static function dateQueryDefaultForm()
	{
		return [
			'before' => '',
			'after' => '',
			'inclusive' => true,
		];
	}

	public static function metaQueryDefaultForm()
	{
		return [
			'key'     => '',
			'value'   => '',
			'compare' => '=',
			'type'    => 'CHAR',
		];
	}

	public static function postTypes()
	{
		return array_values(array_map(
			function (\WP_Post_Type $postType) {
				return [
					'label' => $postType->label,
					'id' => $postType->name
				];
			},
			get_post_types(
				['public' => true, 'exclude_from_search' => false],
				'objects',
				'and'
			)
		));
	}

	public static function postStatuses()
	{
		return array_values(array_map(
			function ($status) {
				return [
					'label' => $status->label,
					'id' => $status->name
				];
			},
			get_post_stati(
				[
					'exclude_from_search' => false,
					'show_in_admin_all_list' => true
				],
				'objects',
				'and'
			)
		));
	}

	public static function postTypesTaxonomies($types = [])
	{
		$return = [];

		if (empty($types)) {
			$taxonomies = get_taxonomies([], 'objects');
		} else {
			$taxonomies = get_object_taxonomies($types, 'objects');
		}

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
		$args = apply_filters("meilipress/query/posts/parse/before", $args);

		$dateQuery = Arr::get($args, 'date_query');
		$taxQuery = Arr::get($args, 'tax_query');
		$metaQuery = Arr::get($args, 'meta_query');

		if(isset($dateQuery['queries']) && ! empty($dateQuery['queries'])) {
			$args['date_query'] = static::parseSubQueryArgs($dateQuery);
		}
		else {
			unset($args['date_query']);
		}

		if(isset($taxQuery['queries']) && ! empty($taxQuery['queries'])) {
			$args['tax_query'] = static::parseSubQueryArgs($taxQuery, 'terms');
		}
		else {
			unset($args['tax_query']);
		}

		if(isset($metaQuery['queries']) && ! empty($metaQuery['queries'])) {
			$args['meta_query'] = static::parseSubQueryArgs($metaQuery);
		}
		else {
			unset($args['meta_query']);
		}

		if(! isset($args['post_type']) || empty($args['post_type'])) {
			$args['post_type'] = 'any';
		}

		if(! isset($args['post_status']) || empty($args['post_status'])) {
			$args['post_status'] = 'any';
		}

		if(empty($args['author'])) {
			unset($args['author']);
		}

		if(empty($args['author_name'])) {
			unset($args['author_name']);
		}

		if(empty($args['post_parent'])) {
			unset($args['post_parent']);
		}

		return apply_filters("meilipress/query/posts/parse", $args);
	}

	public static function parseSubQueryArgs($subQuery, $split = null)
	{
		$newSubQuery = [];

		if($subQuery && isset($subQuery['queries'])) {
			$queries = $subQuery['queries'];
			unset($subQuery['queries']);

			if($split) {
				$queries = array_map(function($subQuery) use ($split) {
					if(isset($subQuery[$split]) && is_string($subQuery[$split])) {
						$subQuery[$split] = array_map('trim', explode(',', $subQuery[$split]));
					}

					return $subQuery;
				}, $queries);
			}

			$newSubQuery = array_merge($subQuery, $queries);
		}

		return $newSubQuery;
	}

	public static function count($args)
	{
		$args = static::parseArgs($args);
		$args['fields'] = 'ids';
		$args['posts_per_page'] = -1;

		$query = new \WP_Query($args);

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