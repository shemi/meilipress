<?php

namespace Shemi\MeiliPress\Http\Controllers;

use Shemi\MeiliPress\Index;
use Shemi\MeiliPress\Query\PostsQuery;

class IndexCrudController extends Controller
{

	public function save()
	{
		$id = $this->request->get('id', null);
		$name = $this->request->get('name');
		$status = $this->request->get('status');
		$query = $this->request->get('data.query');
		$fields = (array) $this->request->get('data.fields', []);
		$synonyms = (array) $this->request->get('data.synonyms', []);
		$stopWords = (array) $this->request->get('data.stop_words', []);
		$rankinRules = (array) $this->request->get('data.ranking_rules', []);

		if(! $name) {
			$this->validationErrors([
				'name' => __("The index name is required!")
			]);
		}

		$exists = Index::findByName($name);

		if($exists && $exists->id() !== $id) {
			$this->validationErrors(['name' => sprintf(__('Index with the same name (%1$s) already exists!', MP_TD), $name)]);
		}

		$index = $id ? Index::find($id) : Index::create();

		if(! $index) {
			$this->error(__("Index with the ID: {$id} cannot be found", MP_TD), [], 404);
		}

		$index->name($name);
		$index->status($status);
		$index->query($query);
		$index->fields($fields);
		$index->synonyms($synonyms);
		$index->stopWords($stopWords);
		$index->rankingRules($rankinRules);
		$index->save();

		if(! $id) {
			$page = $this->plugin->provider('pages')->getPage('meilipress-index');

			$this->redirect(
				$page->url(['id' => $index->id(), 'new' => '1']),
				['content' => __("Index saved!", MP_TD)]
			);
		}

		return [
			'content' => __("Index saved!", MP_TD),
			'index' => $index->toView()
		];
	}

	public function postCount()
	{
		$id = $this->request->get('id', null);
		$query = $this->request->get('data.query');
		$index = $id ? Index::find($id) : Index::create();

		if(! $index) {
			return ['count' => 0];
		}

		return ['count' => $index->queryType::count($query)];
	}

}