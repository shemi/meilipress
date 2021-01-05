<?php

namespace Shemi\MeiliPress;

use Illuminate\Support\Arr;
use Shemi\Core\Support\ServiceProvider;

class MeiliPressServiceProvider extends ServiceProvider
{

	/**
	 * @var MeiliPress $plugin
	 */
	protected $plugin;

	public function register()
	{
		if (is_admin()) {
			$this->checkIfEnabled();
		}

		if (!$this->plugin->disabled()) {
			add_action('save_post', [$this, 'onPostSaved'], 10, 3);
			add_action('delete_post', [$this, 'onPostDelete'], 10, 2);
			add_action('saved_term', [$this, 'onTermChange'], 10, 3);
			add_action('delete_term', [$this, 'onTermDeleted'], 10, 5);
		}

	}

	public function checkIfEnabled()
	{
		if ($this->plugin->disabled()) {
			$this->plugin->messages()->error(
				sprintf(
					__('<b>MeiliPress is disabled:</b> go to the <a href="%1$s">settings</a> and configure your MeiliSearch instance connection', MP_TD),
					$this->plugin->getPageUrl("meilipress-settings")
				),
				false,
				'mp-disabled-notice'
			);
		}
	}

	public function onPostSaved($postId, $post, $update)
	{
		/** @var Index $index */
		foreach ($this->plugin->indexes() as $index) {
			$index->maybeUpdatePostDocument($post);
		}
	}

	public function onPostDelete($postId, $post)
	{
		foreach ($this->plugin->indexes() as $index) {
			$index->maybeDeleteDocument($post);
		}
	}

	public function onTermChange($termId, $ttId, $taxonomy)
	{
		/** @var Index $index */
		foreach ($this->plugin->indexes() as $index) {
			$taxonomies = $index->queryType::postTypesTaxonomies(isset($index->query()['post_type']) ? $index->query()['post_type'] : []);
			$taxonomies = Arr::pluck($taxonomies, 'id');

			if (!in_array($taxonomy, $taxonomies)) {
				continue;
			}

			$ids = $index->queryType::getRelatedObjectIds($index->query(), [$termId], $taxonomy);
			$needSync = $index->postsNeedSync($ids, false);

			if(! empty($needSync)) {
				$this->plugin->syncJob($index)->sync($needSync);
			}
		}
	}

	public function onTermDeleted($termId, $ttId, $taxonomy, $term, $objectIds)
	{
		/** @var Index $index */
		foreach ($this->plugin->indexes() as $index) {
			$taxonomies = $index->queryType::postTypesTaxonomies(isset($index->query()['post_type']) ? $index->query()['post_type'] : []);
			$taxonomies = Arr::pluck($taxonomies, 'id');

			if (!in_array($taxonomy, $taxonomies)) {
				continue;
			}

			$needSync = $index->postsNeedSync($objectIds, false);

			if(! empty($needSync)) {
				$this->plugin->syncJob($index)->sync($needSync);
			}
		}
	}

}