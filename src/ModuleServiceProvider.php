<?php

namespace KodePandai\Modular;

use Illuminate\Routing\Router;
use Illuminate\Support\Arr;
use Illuminate\Support\ServiceProvider;
use KodePandai\Modular\Exceptions\InvalidModuleException;
use ReflectionClass;

abstract class ModuleServiceProvider extends ServiceProvider
{
    protected Module $module;

    abstract public function configureModule(Module $module): void;

    public function register(): void
    {
        $this->registeringModule();

        $this->module = new Module();

        $this->module->setBasePath($this->getModuleBaseDir());

        $this->configureModule($this->module);

        if (empty($this->module->name)) {
            throw InvalidModuleException::nameIsRequired();
        }

        foreach ($this->module->configFileNames as $configFileName) {
            $path = $this->module->basePath("/../config/{$configFileName}.php");
            $this->mergeConfigFrom($path, $configFileName);
        }

        $this->moduleRegistered();
    }

    public function boot(): void
    {
        $this->bootingModule();

        if ($this->module->hasViews) {
            $this->loadViewsFrom(
                $this->module->basePath('/../resources/views'),
                $this->module->name
            );
        }

        foreach ($this->module->viewComponents as $componentClass => $prefix) {
            $this->loadViewComponentsAs($prefix, [$componentClass]);
        }

        if ($this->module->hasTranslations) {
            $this->loadTranslationsFrom(
                $this->module->basePath('/../resources/lang/'),
                $this->module->name
            );
        }

        foreach ($this->module->migrationPaths as $migrationPath) {
            $this->loadMigrationsFrom(
                $this->module->basePath("/../${migrationPath}")
            );
        }

        foreach ($this->module->routeFileNames as $routeFileName) {
            $this->loadRoutesFrom(
                "{$this->module->basePath('/../routes/')}{$routeFileName}.php"
            );
        }

        if (! empty($this->module->commands)) {
            $this->commands($this->module->commands);
        }

        foreach ($this->module->helpers as $helperPath) {
            require_once $this->module->basePath("/../{$helperPath}");
        }

        /** @var Router $router */
        $router = $this->app->make(Router::class);
        foreach ($this->module->middlewares as $alias => $middleware) {
            $router->aliasMiddleware($alias, $middleware);
        }

        foreach ($this->module->serviceProviders as $serviceProvider) {
            $this->app->register($serviceProvider);
        }

        $this->moduleBooted();
    }

    public function registeringModule(): void
    {
    }

    public function moduleRegistered(): void
    {
    }

    public function bootingModule(): void
    {
    }

    public function moduleBooted(): void
    {
    }

    protected function getModuleBaseDir(): string
    {
        $reflector = new ReflectionClass(get_class($this));

        return dirname($reflector->getFileName());
    }

    /**
     * Merge the given configuration with the existing configuration.
     *
     * @param string $path
     * @param string $key
     */
    protected function mergeConfigFrom($path, $key): void
    {
        $config = $this->app->make('config');

        $config->set($key, $this->mergeArray(
            require $path,
            $config->get($key, [])
        ));
    }

    /**
     * Merges the configs together and takes multi-dimensional arrays into account.
     *
     * @see https://gist.github.com/koenhoeijmakers/0a8e326ee3b12a826d73be38693fb647
     */
    protected function mergeArray(array $original, array $merging): array
    {
        $array = array_merge($original, $merging);

        foreach ($original as $key => $value) {
            if (! is_array($value) || ! Arr::exists($merging, $key) || is_numeric($key)) {
                continue;
            }
            $array[$key] = $this->mergeArray($value, $merging[$key]);
        }

        return $array;
    }
}
