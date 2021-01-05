<?php

namespace Shemi\MeiliPress\Pages;

use Shemi\Core\Foundation\Pages\Page;
use Shemi\MeiliPress\MeiliPress;

class DashboardPage extends Page
{

	public function __construct(MeiliPress $plugin)
	{
		$this->title = __("MeiliPress Dashboard", MP_TD);
		$this->menuTitle = __("MeiliPress", MP_TD);
		$this->slug = "meilipress";
		$this->icon = 'data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' fill=\'none\' viewBox=\'0 0 56 57\'%3E%3Cdefs/%3E%3Crect width=\'42.711\' height=\'42.728\' fill=\'url(%23paint0_linear)\' rx=\'15.477\' transform=\'rotate(23.011 6.3 41.406) skewX(.023)\'/%3E%3Cpath fill=\'url(%23paint1_linear)\' fill-rule=\'evenodd\' d=\'M9.762 36.857c-2.363-5.57-3.545-8.355-3.618-10.933A13.676 13.676 0 0110.25 15.76c1.843-1.803 4.626-2.985 10.19-5.348 5.565-2.364 8.348-3.546 10.925-3.62a13.66 13.66 0 0110.158 4.105c1.803 1.843 2.985 4.628 5.348 10.199 2.363 5.57 3.545 8.356 3.618 10.934a13.676 13.676 0 01-4.106 10.162c-1.843 1.804-4.626 2.985-10.19 5.349-5.565 2.363-8.348 3.545-10.925 3.619a13.661 13.661 0 01-10.159-4.104c-1.802-1.844-2.984-4.63-5.347-10.2z\' clip-rule=\'evenodd\'/%3E%3Cpath fill=\'%23fff\' fill-rule=\'evenodd\' d=\'M34.529 21.283c3.567 0 6.212 2.616 6.212 6.863v8.524h-5.013v-7.724c0-2.123-1.015-3.139-2.676-3.139-.769 0-1.538.338-2.276 1.23.031.37.062.74.062 1.109v8.524h-4.983v-7.724c0-2.123-1.045-3.139-2.675-3.139-.77 0-1.508.37-2.246 1.323v9.54h-4.982V21.745h4.982v.892c1.015-.861 2.061-1.354 3.722-1.354 1.876 0 3.475.708 4.582 2 1.569-1.384 3.076-2 5.29-2z\' clip-rule=\'evenodd\'/%3E%3Cdefs%3E%3ClinearGradient id=\'paint0_linear\' x1=\'-18.833\' x2=\'21.372\' y1=\'20.102\' y2=\'62.815\' gradientUnits=\'userSpaceOnUse\'%3E%3Cstop stop-color=\'%23E41359\'/%3E%3Cstop offset=\'1\' stop-color=\'%23F23C79\'/%3E%3C/linearGradient%3E%3ClinearGradient id=\'paint1_linear\' x1=\'1.886\' x2=\'17.653\' y1=\'18.293\' y2=\'55.416\' gradientUnits=\'userSpaceOnUse\'%3E%3Cstop stop-color=\'%2324222F\'/%3E%3Cstop offset=\'1\' stop-color=\'%232B2937\'/%3E%3C/linearGradient%3E%3C/defs%3E%3C/svg%3E';
		$this->position = 95;

		parent::__construct($plugin);
	}

	public function subPage()
	{
		return [
			'title' => __("Dashboard", MP_TD)
		];
	}

	public function boot()
	{

	}

	public function render()
	{
		echo $this->plugin()->view('admin.dashboard', [

		]);
	}

}