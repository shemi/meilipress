<?php

namespace Shemi\MeiliPress;

use Shemi\Core\Support\ServiceProvider;

class AssetsServiceProvider extends ServiceProvider
{

	/**
	 * @var MeiliPress $plugin
	 */
	protected $plugin;

	public function register()
	{
		add_action('admin_enqueue_scripts', [$this, 'adminAssets']);
	}

	public function adminAssets()
	{
		wp_enqueue_style(
			'mp-admin-general-style',
			$this->plugin->publicUri('css/meilipress-admin-general.css'),
			[],
			$this->plugin->version()
		);

		if($this->plugin->isAdminActive()) {
			wp_enqueue_style(
				'mp-admin-style',
				$this->plugin->publicUri('css/meilipress.css'),
				[],
				$this->plugin->version()
			);

			wp_enqueue_script(
				'mp-admin-app',
				$this->plugin->publicUri('js/meilipress.js'),
				['jquery'],
				$this->plugin->version(),
				true
			);

			wp_localize_script(
				'mp-admin-app',
				'MeiliPress',
				array_merge(
					$this->plugin->getSharedVariables(),
					[
						'i18n' => include $this->plugin->basePath("plugin/i18n.php")
					]
				)
			);
		}

	}

}