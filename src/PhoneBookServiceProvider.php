<?php

namespace Ogilo\PhoneBook;

use Illuminate\Support\ServiceProvider;
use Ogilo\PhoneBook\Console\InstallCommand;
use Ogilo\PhoneBook\Console\UpdateCommand;
/**
*
*/
class PhoneBookServiceProvider extends ServiceProvider
{

	protected $commands = [
		'Ogilo\PhoneBook\Console\InstallComand'
	];

	function register()
	{
		// print(config('app.name').' in register()');
		$this->app->bind('phonebook',function($app){
			return new PhoneBook;
		});
	}

	public function boot()
	{
		config(['admin.menu.admin-phonebook'=>[
			'caption'=>'PhoneBook',
			'submenu'=>[
				'admin-phonebook-contacts'=>'Contacts',
				'admin-phonebook-contacts-add'=>'Add',
				'admin-phonebook-contacts-export'=>'Export ',
			]
		]]);

		if ($this->app->runningInConsole()) {
			$this->commands([
					// InstallCommand::class,
					// UpdateCommand::class
				]);
		}

		require_once(__DIR__.'/Support/helpers.php');

		$this->loadRoutesFrom(__DIR__.'/../routes/web.php');
		$this->loadRoutesFrom(__DIR__.'/../routes/api.php');
		$this->loadViewsFrom(__DIR__.'/../resources/views','phonebook');
		$this->loadMigrationsFrom(__DIR__.'/../database/migrations');

		$this->publishes([
			__DIR__.'/../database/seeds' => database_path('seeds/vendor/admin'),
		], 'phonebook-database');

		$this->publishes([
			__DIR__.'/../public/img' => public_path('vendor/admin/img'),
			__DIR__.'/../public/css' => public_path('vendor/admin/css'),
			__DIR__.'/../public/js' => public_path('vendor/admin/js'),
			__DIR__.'/../config/phonebook.php' => config_path(''),
		], 'phonebook-public');

		$this->publishes([
			__DIR__.'/../resources/views'=>resource_path('views/vendor/phonebook')
		],'phonebook-views');
	}
}
