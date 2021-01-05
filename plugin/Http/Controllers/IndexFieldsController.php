<?php

namespace Shemi\MeiliPress\Http\Controllers;

use Shemi\MeiliPress\Query\PostsQuery;

class IndexFieldsController extends Controller
{

	public function inspectFunction()
	{
		$functionName = trim($this->request->get('function'));

		if(! $functionName) {
			$this->validationErrors(['function' => __("Missing function name", MP_TD)]);
		}

		if(! function_exists($functionName)) {
			$this->validationErrors([
				'function' => sprintf(__('Function with the name "%1$s" does not exist', MP_TD), $functionName)
			]);
		}

		$refFunction = new \ReflectionFunction($functionName);
		$parameters = $refFunction->getParameters();
		array_shift($parameters);
		$return = [];

		foreach ($parameters as $parameter) {
			$default = $parameter->isOptional() ? $parameter->getDefaultValue() : '';

			if($default === false) {
				$default = 'false';
			}

			if($default === true) {
				$default = 'true';
			}

			$return[] = [
				'name' => $parameter->name,
				'value' => '',
				'default' => (string) $default
			];
		}

		return $return;
	}

	public function getTaxonomies()
	{
		$postTypes = $this->request->get('post_type');

		return PostsQuery::postTypesTaxonomies($postTypes);

	}

}