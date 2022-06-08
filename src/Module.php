<?php

namespace KodePandai\Modular;

class Module
{
    public string $name;

    public array $configFileNames = [];

    public bool $hasViews = false;

    public array $viewComponents = [];

    public array $viewComposers = [];

    public bool $hasTranslations = false;

    public array $migrationPaths = [];

    public array $routeFileNames = [];

    public array $commands = [];

    public array $helpers = [];

    public array $middlewares = [];

    public array $serviceProviders = [];

    public string $basePath;

    public function name(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function hasConfigFile(string $configFileName): self
    {
        $this->configFileNames[] = $configFileName;

        return $this;
    }

    public function hasConfigFiles(array $configFileNames): self
    {
        $this->configFileNames = array_merge(
            $this->configFileNames,
            collect($configFileNames)->flatten()->toArray()
        );

        return $this;
    }

    public function hasViews(): self
    {
        $this->hasViews = true;

        return $this;
    }

    public function hasViewComponent(string $prefix, string $viewComponentName): self
    {
        $this->viewComponents[$viewComponentName] = $prefix;

        return $this;
    }

    public function hasViewComponents(string $prefix, array $viewComponentNames): self
    {
        foreach ($viewComponentNames as $componentName) {
            $this->viewComponents[$componentName] = $prefix;
        }

        return $this;
    }

    public function hasTranslations(): self
    {
        $this->hasTranslations = true;

        return $this;
    }

    public function hasMigrationPath(string $migrationPath): self
    {
        $this->migrationPaths[] = $migrationPath;

        return $this;
    }

    public function hasMigrationPaths(array $migrationPaths): self
    {
        $this->migrationPaths = array_merge(
            $this->migrationPaths,
            collect($migrationPaths)->flatten()->toArray()
        );

        return $this;
    }

    public function hasRoute(string $routeFileName): self
    {
        $this->routeFileNames[] = $routeFileName;

        return $this;
    }

    public function hasRoutes(array $routeFileNames): self
    {
        $this->routeFileNames = array_merge(
            $this->routeFileNames,
            collect($routeFileNames)->flatten()->toArray()
        );

        return $this;
    }

    public function hasCommand(string $commandClassName): self
    {
        $this->commands[] = $commandClassName;

        return $this;
    }

    public function hasCommands(array $commandClassNames): self
    {
        $this->commands = array_merge(
            $this->commands,
            collect($commandClassNames)->flatten()->toArray()
        );

        return $this;
    }

    public function hasHelper(string $helper): self
    {
        $this->helpers[] = $helper;

        return $this;
    }

    public function hasHelpers(array $helpers): self
    {
        $this->helpers = array_merge(
            $this->helpers,
            collect($helpers)->flatten()->toArray()
        );

        return $this;
    }

    public function hasMiddleware(string $alias, string $middleware): self
    {
        $this->middlewares[$alias] = $middleware;

        return $this;
    }

    public function hasMiddlewares(array $middlewares): self
    {
        $this->middlewares[] = $middlewares;

        return $this;
    }

    public function hasServiceProvider(string $serviceProvider): self
    {
        $this->serviceProviders[] = $serviceProvider;

        return $this;
    }

    public function hasServiceProviders(array $serviceProviders): self
    {
        $this->serviceProviders = array_merge(
            $this->serviceProviders,
            collect($serviceProviders)->flatten()->toArray()
        );

        return $this;
    }

    public function basePath(string $directory = null): string
    {
        if ($directory === null) {
            return $this->basePath;
        }

        return $this->basePath.DIRECTORY_SEPARATOR
            .ltrim($directory, DIRECTORY_SEPARATOR);
    }

    public function setBasePath(string $path): self
    {
        $this->basePath = $path;

        return $this;
    }
}
