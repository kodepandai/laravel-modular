<?php

namespace KodePandai\Modular\Providers;

use Illuminate\Contracts\Foundation\CachesConfiguration;
use Illuminate\Routing\Router;
use Illuminate\Support\Arr;
use Illuminate\Support\ServiceProvider;
use KodePandai\Modular\Exceptions\InvalidModuleException;
use KodePandai\Modular\Module;
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

        foreach ($this->module->configFiles as $configName => $configFile) {
            $this->mergeConfigFrom($configFile, $configName);
        }

        if ($this->module->hasHelper) {
            require_once $this->module->helperFile;
        }

        $this->moduleRegistered();
    }

    public function boot(): void
    {
        $this->bootingModule();

        if ($this->module->hasViews) {
            $this->loadViewsFrom($this->module->viewPath, $this->module->name);
        }

        if ($this->module->hasMigrations) {
            $this->loadMigrationsFrom($this->module->migrationPath);
        }

        if ($this->module->hasTranslations) {
            $this->loadTranslationsFrom($this->module->translationPath, $this->module->name);
        }

        foreach ($this->module->routeFiles as $routeFile) {
            $this->loadRoutesFrom($routeFile);
        }

        if (! empty($this->module->commands)) {
            $this->commands($this->module->commands);
        }

        foreach ($this->module->viewComponents as $componentClass => $prefix) {
            $this->loadViewComponentsAs($prefix, [$componentClass]);
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
        // pass
    }

    public function moduleRegistered(): void
    {
        // pass
    }

    public function bootingModule(): void
    {
        // pass
    }

    public function moduleBooted(): void
    {
        // pass
    }

    public function getModule(): Module
    {
        return $this->module;
    }

    protected function getModuleBaseDir(): string
    {
        $reflector = new ReflectionClass(get_class($this));

        return str_replace('/src/Providers', '', dirname($reflector->getFileName()));
    }

    /**
     * @inheritDoc
     */
    protected function mergeConfigFrom($path, $key): void
    {
        if (! ($this->app instanceof CachesConfiguration && $this->app->configurationIsCached())) {
            $config = $this->app->make('config');
            $config->set($key, $this->mergeArray(
                require $path,
                $config->get($key, [])
            ));
        }
    }

    /**
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
