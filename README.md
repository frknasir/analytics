# Add analytics to your Laravel application

[![Latest Version on Packagist](https://img.shields.io/packagist/v/starfolksoftware/analytics.svg?style=flat-square)](https://packagist.org/packages/starfolksoftware/analytics)
[![Build Status](https://img.shields.io/travis/starfolksoftware/analytics/master.svg?style=flat-square)](https://travis-ci.org/starfolksoftware/analytics)
[![Total Downloads](https://img.shields.io/packagist/dt/starfolksoftware/analytics.svg?style=flat-square)](https://packagist.org/packages/starfolksoftware/analytics)

Add the ability to associate analytics to your Laravel Eloquent models. 

```php
$post = Post::find(1);

event(new Viewed($post))
```

## Installation

You can install the package via composer:

```bash
composer require starfolksoftware/analytics
```

The package will automatically register itself.

You can publish the migration with:

```bash
php artisan vendor:publish --provider="StarfolkSoftware\Analytics\AnalyticsServiceProvider" --tag="migrations"
```

After the migration has been published you can create the media-table by running the migrations:

```bash
php artisan migrate
```

You can publish the config-file with:

```bash
php artisan vendor:publish --provider="StarfolkSoftware\Analytics\AnalyticsServiceProvider" --tag="config"
```

To register the `Viewed` event, `CaptureView` and `CaptureVisit` listeners, edit your `EventServiceProvider` as in the following:

```php
  ...
  /**
   * The event listener mappings for the application.
   *
   * @var array
   */
  protected $listen = [
    'StarfolkSoftware\Analytics\Events\Viewed' => [
      'StarfolkSoftware\Analytics\Listeners\CaptureView',
      'StarfolkSoftware\Analytics\Listeners\CaptureVisit',
    ],
  ];
```

## Usage

### Registering Models

To let your models be able to have analytics, add the `HasViews`, `HasVisits` traits to the model classes.

``` php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use StarfolkSoftware\Analytics\Traits\{HasViews, HasVisits};

class Post extends Model
{
  use HasViews, HasVisits;
  ...
}
```

### Usage

To trigger the viewed event on your model, you can call the `event()` helper method. It receives the intance of the `Viewed`.

```php
$post = Post::find(1);

event(new Viewed($post))
```

This event triggers the `CaptureView` and `CaptureVisit` listeners.

### Retrieving Analytics

The models that use the `HasViews` and `HasVisits` traits have access to it's analytics using the `views` and `visits` relations respectively:

```php

$post = Post::find(1);

// Retrieve
$views = $post->views;
$visits = $post->visits;

// Stats
$viewStats = Post::viewStats();
$viewStat = $post->viewStat();
$visitStats = Post::visitStats();
$visitStat = $post->visitStat();

```

### Testing

``` bash
composer test
```

### Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

### Security

If you discover any security related issues, please email frknasir@yahoo.com instead of using the issue tracker.

## Credits

- [Faruk Nasir](https://github.com/frknasir)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
