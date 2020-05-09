<?php

namespace StarfolkSoftware\Analytics\Tests;

use Illuminate\Foundation\Auth\User;
use Illuminate\Database\Schema\Blueprint;
use StarfolkSoftware\Analytics\AnalyticsServiceProvider;

abstract class TestCase extends \Orchestra\Testbench\TestCase
{
  public function setUp(): void
  {
    parent::setUp();
    $this->loadLaravelMigrations(['--database' => 'sqlite']);
    $this->setUpDatabase();
    $this->createUser();
  }

  protected function getPackageProviders($app)
  {
    return [
      AnalyticsServiceProvider::class,
    ];
  }

  protected function getEnvironmentSetUp($app)
  {
    $app['config']->set('auth.providers.users.model', User::class);
    $app['config']->set('database.default', 'sqlite');
    $app['config']->set('database.connections.sqlite', [
      'driver' => 'sqlite',
      'database' => ':memory:',
      'prefix' => '',
    ]);
    $app['config']->set('app.key', 'base64:6Cu/ozj4gPtIjmXjr8EdVnGFNsdRqZfHfVjQkmTlg4Y=');
  }

  protected function setUpDatabase()
  {
    include_once __DIR__ . '/../database/migrations/create_views_table.php.stub';
    include_once __DIR__ . '/../database/migrations/create_visits_table.php.stub';

    (new \CreateViewsTable())->up();
    (new \CreateVisitsTable())->up();

    $this->app['db']->connection()->getSchemaBuilder()->create('posts', function (Blueprint $table) {
      $table->increments('id');
      $table->string('title');
      $table->timestamps();
    });
  }

  protected function createUser()
  {
    User::forceCreate([
      'name' => 'User',
      'email' => 'user@email.com',
      'password' => 'test'
    ]);
  }
}
