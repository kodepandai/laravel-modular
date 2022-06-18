# Laravel Modular

> THIS BRANCH IS WIP!
> TODO: Improve documentation

Modularize your laravel app in a package way. Inspired by 
[`spatie/laravel-package-tools`](https://github.com/spatie/laravel-package-tools).

This laravel modular package is built with the concept 
that each module is considered as a separate package. 
So it will allow you to structure the module folders in the same way
as you build a laravel package.

## Installation

```bash
$ composer require kodepandai/laravel-modular
```

After installation, add this configuration to your `composer.json`

```jsonc
"repositories": [
  // ...
  {
    "type": "path",
    "url": "modules/*/",
    "options": {
      "symlink": true
    }
  }
  // ...
],
```

## Usage

### New Module

```bash
$ php artisan modular:make Sales
```

\*Sales is the module name

### Working with Modular

This package *DOES NOT* load resources (routes, config, migration, etc) 
automatically, instead you need to manually load them in 
your module `ServiceProvider`.

### Module Service Provider

```php
namespace Sales\Providers;

use KodePandai\Modular\Module;
use KodePandai\Modular\Providers\ModuleServiceProvider;

class SalesServiceProvider extends ModuleServiceProvider
{
    public function configureModule(Module $module): void
    {
        $module->name('Sales')
               ->hasViews()
               ->hasMigrations()
               ->hasRoutes();

        // $module->hasOtherThings() ...
    }
}

```

### Configs

Put your config file in the `<module root>/config` folder,
then register it with:

```php
// load all configs
$module->hasConfigs();

// load one config
$module->hasConfig('sales');

// load multiple configs
$module->hasConfigs(['sales', 'services']);
```

### Views

Put your view file in the `<module root>/resources/views` folder,
then register it with:

```php
// load all views
$module->hasViews();
```

### View Components

Put your view component file in the `<module root>/src/Components` folder,
then register it with:

```php
// load one <sales::header /> component
$module->hasViewComponent('sales', \Sales\Components\Header::class);

// load multiple components
$module->hasViewComponents('sales', [
  \Sales\Components\Header::class, // <sales::header />
  \Sales\Components\Sidebar::class, // <sales::sidebar />
]);
```

### Translations

Put your translation file in the `<module root>/lang` 
or `<module root>/resources/lang` folder,
then register it with:

```php
$module->hasTranslations();
```

### Migrations

Put your migration in the `<module root>/database/migrations` folder,
then register it with:

```php
// load all migrations
$module->hasMigrations();
```

Note:
* For factory put in the `<module root>/database/factories` folder
* For seeder put in the `<module root>/database/seeders` folder

### Routes

Put your route file in the `<module root>/routes` folder,
then register it with:

```php
// load all routes
$module->hasRoutes();

// load one route
$module->hasRoute('web');

// load multiple routes
$module->hasRoutes(['web', 'api']);
```

### Commands

Put your command file in the `<module root>/src/Commands` folder,
then register it with:

```php
// load all commands
$module->hasCommmands();

// load one command
$module->hasCommand(\Sales\Commands\CheckServer::class);

// load multiple commands
$module->hasCommands([
  \Sales\Commands\CheckVersion::class,
  \Sales\Commands\CheckServer::class,
]);
```

### Helpers

Put your helper function in the `<module root>/src/helpers.php` file,
then register it with:

```php
// load helper
$module->hasHelper();
```

### Middlewares

Put your middleware file int the `<module root>/src/Http/Middleware/` folder,
then register it with:

```php
// load one middleware
$module->hasMiddleware('sales.admin', \Sales\Http\Middleware\EnsureAdminHaveAccessToSales::class);

// load multiple middlewares
$module->hasMiddlewares([
  'sales.admin' => \Sales\Http\Middleware\EnsureAdminHaveAccessToSales::class,
  'sales.whitelist' => \Sales\Http\Middleware\OnlyWhitelistIpCanAccessSales::class,
]);
```

### Service Providers

Put your service provider in the `<module root>/src/Providers` folder
then register it with:

```php
// load one service Provider
$module->hasServiceProvider(\Sales\Providers\PaymentServiceProvider::class);

// load multiple service providers
$module->hasServiceProviders([
  \Sales\Providers\PaymentServiceProvider::class,
  \Sales\Providers\EventServiceProvider::class,
]);
```

## Develop

Run `composer test` to test.
