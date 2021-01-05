<?php

return [

	'options' => [
		\Shemi\MeiliPress\Options\SettingsOptions::class,
		\Shemi\MeiliPress\Options\IndexesOptions::class,
		\Shemi\MeiliPress\Options\StateOptions::class,
	],

	'pages' => [
		\Shemi\MeiliPress\Pages\DashboardPage::class,
		\Shemi\MeiliPress\Pages\IndexesPage::class,
		\Shemi\MeiliPress\Pages\IndexPage::class,
		\Shemi\MeiliPress\Pages\SettingsPage::class
	],

	'controllers' => [
		\Shemi\MeiliPress\Http\Controllers\IndexCrudController::class,
		\Shemi\MeiliPress\Http\Controllers\SettingsController::class,
		\Shemi\MeiliPress\Http\Controllers\IndexFieldsController::class,
		\Shemi\MeiliPress\Http\Controllers\IndexesController::class
	],

	'providers' => [
		'messages' => \Shemi\MeiliPress\MessagesServiceProvider::class,
		\Shemi\MeiliPress\MeiliPressServiceProvider::class,
		\Shemi\MeiliPress\AssetsServiceProvider::class
	],

	'commends' => [
		\Shemi\MeiliPress\Console\Commands\SyncCommand::class
	]

];