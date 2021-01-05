<?php

namespace Shemi\MeiliPress\Pages;

use Shemi\Core\Foundation\Pages\Page;
use Shemi\Core\Foundation\Plugin;
use Shemi\MeiliPress\MeiliPress;

/**
 * Class Settings
 *
 * @property MeiliPress $plugin
 * @method  MeiliPress plugin()
 *
 * @package Shemi\MeiliPress\Pages
 */
class SettingsPage extends Page
{

	public function __construct(MeiliPress $plugin)
	{
		$this->title = __("MeiliPress - Settings", MP_TD);
		$this->menuTitle = __("Settings", MP_TD);
		$this->slug = "meilipress-settings";

		parent::__construct($plugin);
	}

	public function parent()
	{
		return "meilipress";
	}

	public function boot()
	{

	}

	public function render()
	{
		$data = $this->plugin()->settings()->all();

		echo $this->plugin->view('admin.settings', [
			'pageTitle' => $this->title(),
			'data' => $data
		]);
	}

}