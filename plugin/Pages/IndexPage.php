<?php

namespace Shemi\MeiliPress\Pages;

use Illuminate\Support\Str;
use Shemi\Core\Foundation\Pages\Page;
use Shemi\Core\Foundation\Plugin;
use Shemi\MeiliPress\Documents\PostDocument;
use Shemi\MeiliPress\Index as IndexModel;
use Shemi\MeiliPress\MeiliPress;
use Shemi\MeiliPress\Query\PostsQuery;

class IndexPage extends Page
{
	/**
	 * @var MeiliPress $plugin
	 */
	protected $plugin;

	public function __construct(Plugin $plugin)
	{
		$this->title = __("MeiliPress - Index", MP_TD);
		$this->menuTitle = __("Index", MP_TD);
		$this->slug = "meilipress-index";

		parent::__construct($plugin);
	}

	public function parent()
	{
		return "meilipress-indexes";
	}

	public function boot()
	{
		if($this->request->get('new') === '1') {
			$this->plugin->messages()->success(__("Index saved!", MP_TD));
		}

		add_action('admin_enqueue_scripts', function() {
			$this->plugin()->share([
				'index_data' => [
					'form' => $this->form(),
					'availableFields' => PostDocument::availableFields()->map->toView()->all(),
					'postTypes' => PostsQuery::postTypes(),
					'taxonomies' => PostsQuery::postTypesTaxonomies(),
					'taxQueryDefaultForm' => PostsQuery::taxQueryDefaultForm(),
					'dateQueryDefaultForm' => PostsQuery::dateQueryDefaultForm(),
					'metaQueryDefaultForm' => PostsQuery::metaQueryDefaultForm(),
					'statuses' => PostsQuery::postStatuses(),
					'rankingRulesDescriptions' => [
						"typo" => __('Results are sorted by increasing number of typos: find documents that match query terms with fewer typos first.', MP_TD),
						"words" => __('Results are sorted by decreasing number of matched query terms in each matching document: find documents that contain more occurrences of the query terms first.', MP_TD),
						"proximity" => __('Results are sorted by increasing distance between matched query terms: find documents that contain more query terms found close together (close proximity between two query terms) and appearing in the original order specified in the query string first.', MP_TD),
						"attribute" => __('Results are sorted according to the order of importance of the attributes: find documents that contain query terms in more important attributes first.', MP_TD),
						"wordsPosition" => __('Results are sorted by the position of the query words in the attributes: find documents that contain query terms earlier in their attributes first.', MP_TD),
						"exactness" => __('Results are sorted by the similarity of the matched words with the query words: find documents that contain exactly the same terms as the ones queried first.', MP_TD),
						"asc" => __('A custom rule: results sorted by increasing value of the attribute', MP_TD),
						"desc" => __('A custom rule: results sorted by decreasing value of the attribute', MP_TD),
					]
				]
			]);
		}, 1);
	}

	protected function form()
	{
		$id = $this->request->get('id');

		if(! $id || ! $index = IndexModel::find($id)) {
			return IndexModel::create()->toView();
		}

		return $index->toView();
	}

	public function render()
	{
		echo $this->plugin()->view('admin.index', [
			'page' => $this,
			'plugin' => $this->plugin()
		]);
	}

}