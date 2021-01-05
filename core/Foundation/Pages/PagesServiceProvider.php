<?php

namespace Shemi\Core\Foundation\Pages;

use Illuminate\Support\Collection;
use Shemi\Core\Support\ServiceProvider;

class PagesServiceProvider extends ServiceProvider
{

	/**
	 * @var Collection $_pages
	 */
	protected $_pages;

	public function register()
	{
		$this->_pages = Collection::make([]);

		add_action('admin_menu', [$this, 'registerPages']);
	}

	public function addPage($pageClass)
	{
		$page = new $pageClass($this->plugin);

		if(! ($page instanceof Page)) {
			throw new \Exception("Pages must be instance of ".Page::class);
		}

		$this->_pages->push($page);

		return $this;
	}

	/**
	 * @param $slug
	 * @return Page|null
	 */
	public function getPage($slug)
	{
		return $this->_pages->first(function(Page $page) use ($slug) {
			return $page->slug() === $slug;
		});
	}

	/**
	 * @return Collection
	 */
	public function all()
	{
		return new Collection($this->_pages->all());
	}

	public function registerPages()
	{
		do_action('sp_core_register_pages', $this);

		$parentPages = $this->_pages->filter(function(Page $page) {
			return ! $page->parent();
		});

		/** @var Page $page */
		foreach ($parentPages as $page) {
			if(! $page->enabled()) {
				continue;
			}

			$page->boot();

			$hook = add_menu_page(
				$page->title(),
				$page->menuTitle(),
				$page->capability(),
				$page->slug(),
				'',
				$page->icon(),
				$page->position()
			);

			if($subPage = $page->subPage()) {
				$subPageTitle = $subPage['title'] ?? $page->title();

				add_submenu_page(
					$page->slug(),
					$subPageTitle,
					$subPage['menuTitle'] ?? $subPageTitle,
					$subPage['capability'] ?? $page->capability(),
					$page->slug()
				);
			}

			$page->setHookSuffix($hook);

			add_action($hook, [$page, 'render']);
		}

		$subPages = $this->_pages->filter(function(Page $page) {
			return (bool) $page->parent();
		});

		foreach ($subPages as $page) {
			if(! $page->enabled()) {
				continue;
			}

			$page->boot();

			$hook = add_submenu_page(
				$page->parent(),
				$page->title(),
				$page->menuTitle(),
				$page->capability(),
				$page->slug(),
				'',
				$page->position()
			);

			$page->setHookSuffix($hook);

			add_action($hook, [$page, 'render']);
		}

	}

}