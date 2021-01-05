<?php

namespace Shemi\Core\Foundation\Pages;

use Shemi\Core\Foundation\Http\Request;
use Shemi\Core\Foundation\Plugin;

abstract class Page
{

	protected $slug = "page";

	protected $title = "Page";

	protected $menuTitle = null;

	protected $icon = 'dashicons-carrot';

	protected $capability = 'manage_options';

	protected $position = 10;

	protected $hookSuffix = null;

	protected $plugin;

	protected $request;

	public function __construct(Plugin $plugin)
	{
		$this->plugin = $plugin;
		$this->request = $plugin->request();
	}

	public function plugin()
	{
		return $this->plugin;
	}

	public function title()
	{
		return $this->title;
	}

	public function menuTitle()
	{
		return $this->menuTitle ?: $this->title();
	}

	public function slug()
	{
		return $this->slug;
	}

	public function icon()
	{
		return $this->icon;
	}

	public function capability()
	{
		return $this->capability;
	}

	public function position()
	{
		return $this->position;
	}

	/**
	 * @return null|array
	 */
	public function subPage()
	{
		return null;
	}

	public function parent()
	{
		return null;
	}

	public function enabled()
	{
		return true;
	}

	public function hookSuffix()
	{
		return $this->hookSuffix;
	}

	public function url($args = [])
	{
		return $this->plugin()->getPageUrl($this->slug(), $args);
	}

	public function setHookSuffix($hookSuffix)
	{
		$this->hookSuffix = $hookSuffix;

		return $this;
	}

	abstract public function boot();

	abstract public function render();

}