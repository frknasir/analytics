<?php

namespace StarfolkSoftware\Analytics;

use Illuminate\Support\ServiceProvider;

class AnalyticsServiceProvider extends ServiceProvider
{
  /**
   * Bootstrap the application services.
   */
  public function boot()
  {
    if ($this->app->runningInConsole()) {
      $this->publishes([
        __DIR__.'/../config/config.php' => config_path('analytics.php'),
      ], 'config');

      if (! class_exists('CreateViewsTable')) {
        $this->publishes([
          __DIR__.'/../database/migrations/create_views_table.php.stub' => database_path('migrations/'.date('Y_m_d_His', time()).'_create_views_table.php'),
        ], 'migrations');
      }

      if (! class_exists('CreateVisitsTable')) {
        $this->publishes([
          __DIR__.'/../database/migrations/create_visits_table.php.stub' => database_path('migrations/'.date('Y_m_d_His', time()).'_create_visits_table.php'),
        ], 'migrations');
      }
    }
  }

  /**
   * Register the application services.
   */
  public function register()
  {
    $this->mergeConfigFrom(__DIR__.'/../config/config.php', 'factchecks');
  }
}
