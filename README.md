# Laravel Modular

Pustaka laravel untuk modularisasi kode secara rapi dan mudah di maintain. 

## Instalasi

```bash
$ composer require kodepandai/laravel-modular
```

Setelah terpasang, lakukan inisiasi pustaka modular

```bash
$ php artisan modular:install
```

Kemudian tambahkan konfigurasi berikut ini di `composer.json`

```jsonc
"extra": {
  // ...
  "merge-plugin": {
    "include": ["modules/*/composer.json"]
  }
  // ...
},
```

## Penggunaan

### Selayang Pandang

Laravel modular ini dibuat dengan konsep bahwa tiap modul dianggap 
sebagai sebuah pustaka tersendiri. Jadi struktur folder dan kondisinya
sama seperti ketika kita membuat pustaka laravel.

### Modul Baru

```bash
$ php artisan modular:make Sales
```

\*Sales adalah nama modul

### Bekerja dengan modular

Konsep modular pada umumnya adalah semua hal (routes, config, migrations, dsb)
di muat di awal tanpa perlu konfigurasi. Tetapi di laravel modular 
ini, tiap hal yang ingin di muat, harus di konfigurasi manual 
di `ServiceProvider` tiap modul. Mengapa? agar yang dimuat adalah
benar-benar apa yang ingin dimuat. Be mindful.

#### Modul Service Provider

Contoh modul service provider:

```php
namespace Sales;

use Illuminate\Support\Facades\Route;
use KodePandai\Modular\Module;
use KodePandai\Modular\ModuleServiceProvider;

class SalesServiceProvider extends ModuleServiceProvider
{
    public function configureModule(Module $module): void
    {
        $module->name('Sales')
            ->hasConfigFile('sales')
            // -> dst...
            ->hasMigrationPath('database/migrations');
    }
}

```

##### Configs

Letakkan konfigurasi di folder `<module root>/config`,
lalu registrasikan dengan:

```php
$module->hasConfigFile('example-config');
// atau
$module->hasConfigFiles(['example-config-1', 'example-config-2']);
```

##### Views

Letakkan templat views di folder `<module root>/resources/views`,
lalu registrasikan dengan:

```php
$module->hasViews();
```

##### View Components

Letakkan komponen view di folder `<module root>/src/Components`,
lalu registrasikan dengan:

```php
$module->hasViewComponent('sales', \Sales\Components\header::class);
// lalu panggil di blade dengan <sales::header />, atau
$module->hasViewComponents('sales', [
  \Sales\Components\Header::class,
  \Sales\Components\Sidebar::class,
]);
// lalu panggil di blade dengan <sales::header /> atau <sales::sidebar />
```

##### Translations

Letakkan file terjemahan di folder `<module root>/resources/lang`,
lalu registrasikan dengan:

```php
$module->hasTranslations();
```

##### Migrations

Letakkan migrasi, factory dan seeder di folder `<module root>/database`,
lalu registrasikan dengan:

```php
$module->hasMigrationPath('database/migrations');
// atau
$module->hasMigrationPaths([
  'migrations/sales',
  'migrations/accounting',
]);
```

##### Routes

Letakkan file route di folder `<module root>/routes`,
lalu registrasikan dengan:

```php
$module->hasRoute('web');
// atau
$module->hasRoutes(['web', 'api']);
```

##### Commands

Letakkan perintah konsol artisan di folder `<module root>/src/Commands`,
lalu registrasikan dengan:

```php
$module->hasCommand(\Module\Commands\CheckVersion::class);
// atau
$module->hasCommands([
  \Module\Commands\CheckVersion::class,
  \Module\Commands\CheckServer::class,
]);
```

##### Helpers

Letakkan file helper di folder `<module root>/src/helpers.php`,
lalu registrasikan dengan:

```php
$module->hasHelper('src/helpers.php');
// atau
$module->hasHelpers([
  'src/Helpers/str.php', 
  'src/Helpers/array.php',
]);
```

##### Middlewares

Letakkan middleware di folder `<module root>/src/Http/Middleware/`,
lalu registrasikan dengan:

```php
$module->hasMiddleware('admin', \Module\Http\Middleware\OnlyAdminAllowed::class);
// atau
$module->hasMiddlewares([
  'admin' => \Module\Http\Middleware\OnlyAdminAllowed::class,
  'reviewer' => \Module\Http\Middleware\OnlyReviewerAllowed::class,
]);
```

##### Service Providers

Apabila ada provider lain dalam modul, letakkan di folder `<module root>/src/Providers`,
lalu registrasikan dengan:

```php
$module->hasServiceProvider(\Module\Providers\PaymentServiceProvider::class);
// atau
$module->hasServiceProviders([
  \Module\Providers\PaymentServiceProvider::class,
  \Module\Providers\EventServiceProvider::class,
]);
```

## Pengembang

* Untuk tes jalankan `composer test`
