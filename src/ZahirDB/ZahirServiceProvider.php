<?php 
namespace ZahirDB;

use Firebird\FirebirdServiceProvider;
use Firebird\DatabaseManager as FirebirdDatabaseManager;
use ZahirDB\ZahirConnectionFactory;

class ZahirServiceProvider extends FirebirdServiceProvider {
	/**
   * Register the application services.
   * This is where the connection gets registered
   *
   * @return void
   */
  	public function register()
  	{
		$this->registerQueueableEntityResolver();

		// The connection factory is used to create the actual connection instances on
		// the database. We will inject the factory into the manager so that it may
		// make the connections while they are actually needed and not of before.
		$this->app->singleton('db.factory', function($app)
		{
			return new ZahirConnectionFactory($app);
		});

		// The database manager is used to resolve various connections, since multiple
		// connections might be managed. It also implements the connection resolver
		// interface which may be used by other components requiring connections.
		$this->app->singleton('db', function($app)
		{
			return new FirebirdDatabaseManager($app, $app['db.factory']);
		});
	}

}
