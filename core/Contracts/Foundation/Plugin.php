<?php

namespace Shemi\Core\Contracts\Foundation;

use Shemi\Core\Contracts\Container\Container;

interface Plugin extends Container
{
	public function basePath($path = "");
}