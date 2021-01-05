<?php

namespace Shemi\MeiliPress;

use Illuminate\Support\Collection;
use Shemi\Core\Foundation\Plugin;
use Shemi\Core\Support\ServiceProvider;

class MessagesServiceProvider extends ServiceProvider
{

	protected $notices;

	public function __construct(Plugin $plugin)
	{
		$this->notices = new Collection([]);

		parent::__construct($plugin);
	}

	public function register()
	{
		add_action('admin_notices', [$this, 'renderNotices']);
	}

	public function add($content, $type, $dismissible = true, $id = null)
	{
		$this->notices->push(
			compact(
				'content',
				'type',
				'dismissible',
				'id'
			)
		);
	}

	public function success($content, $dismissible = true, $id = null)
	{
		$this->add($content, 'success', $dismissible, $id);
	}

	public function error($content, $dismissible = true, $id = null)
	{
		$this->add($content, 'error', $dismissible, $id);
	}

	public function warning($content, $dismissible = true, $id = null)
	{
		$this->add($content, 'warning', $dismissible, $id);
	}

	public function info($content, $dismissible = true, $id = null)
	{
		$this->add($content, 'info', $dismissible, $id);
	}

	public function renderNotices()
	{
		foreach ($this->notices as $index => $notice) {
			$class = "notice mp-notice notice-{$notice['type']}".($notice['dismissible'] ? ' is-dismissible' : '');
			$id = $notice['id'] ?: 'mp-notice-'.$index;
			$notice['content'] = sprintf(__('<b>MeiliPress:</b> %1$s', MP_TD), $notice['content']);

			printf( '<div class="%1$s" id="%2$s"><p>%3$s</p></div>', esc_attr($class), esc_attr($id), $notice['content']);
		}
	}

}