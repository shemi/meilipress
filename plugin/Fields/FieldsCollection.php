<?php

namespace Shemi\MeiliPress\Fields;

use Illuminate\Support\Collection;
use Shemi\MeiliPress\Contracts\ToView;

class FieldsCollection extends Collection implements ToView
{

	public function toView()
	{
		return array_map(function ($value) {
			return $value instanceof ToView ? $value->toView() : $value;
		}, $this->items);
	}

	public function onlyDisplay()
	{
		return $this->filter(function(Field $field) {
			return $field->displayable;
		});
	}

}