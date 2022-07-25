<?php

namespace KodePandai\Modular;

use Exception;
use Illuminate\Support\Facades\File;
use Symfony\Component\Finder\SplFileInfo;

class Module
{
    public string $name;

    public string $basePath;

    public bool $hasViews = false;

    public string $viewPath = '';

    public bool $hasMigrations = false;

    public string $migrationPath = '';

    public bool $hasTranslations = false;

    public string $translationPath = '';

    public bool $hasHelper = false;

    public string $helperFile = '';

    public array $configFiles = [];

    public array $routeFiles = [];

    public array $commands = [];

    public array $viewComponents = [];

    public array $middlewares = [];

    public array $serviceProviders = [];

    public function name(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function hasViews(): self
    {
        $this->hasViews = true;
        $this->viewPath = $this->basePath('resources'.DIRECTORY_SEPARATOR.'views');

        return $this;
    }

    public function hasMigrations(): self
    {
        $this->hasMigrations = true;
        $this->migrationPath = $this->basePath('database'.DIRECTORY_SEPARATOR.'migrations');

        return $this;
    }

    public function hasTranslations(): self
    {
        $this->hasTranslations = true;
        $this->translationPath = $this->basePath('lang');

        return $this;
    }

    public function hasHelper(): self
    {
        $this->hasHelper = true;
        $this->helperFile = $this->basePath('helpers.php');

        if (! file_exists($this->helperFile)) {
            throw new Exception("File {$this->helperFile} not found!");
        }

        return $this;
    }

    public function hasConfig(string $configName): self
    {
        return $this->hasConfigs([$configName]);
    }

    public function hasConfigs(array $configNames = []): self
    {
        if (! empty($configNames)) {
            //.
            $configFiles = [];

            foreach ($configNames as $configName) {
                //.
                $path = $this->basePath('config'.DIRECTORY_SEPARATOR.$configName.'.php');
                if (! file_exists($path)) {
                    throw new \Exception("Config file {$configName} does not exists!");
                }
                $configFiles[$configName] = $path;
            }
        } else {
            //.
            $configFiles = $this->scanConfigs();
        }

        $this->configFiles = array_merge($this->configFiles, $configFiles);

        return $this;
    }

    private function scanConfigs(): array
    {
        $files = $this->getPhpFilesFromDirectory($this->basePath('config'));

        $configFiles = [];
        foreach ($files as $file) {
            $name = str_replace('.php', '', $file->getBaseName());
            $configFiles[$name] = $file->getPathname();
        }

        return $configFiles;
    }

    public function hasRoute(string $routeName): self
    {
        return $this->hasRoutes([$routeName]);
    }

    public function hasRoutes(array $routeNames = []): self
    {
        if (! empty($routeNames)) {
            //.
            $routeFiles = [];

            foreach ($routeNames as $routeName) {
                //.
                $path = $this->basePath('routes'.DIRECTORY_SEPARATOR.$routeName.'.php');
                if (! file_exists($path)) {
                    throw new \Exception("Route file {$routeName} does not exists!");
                }
                $routeFiles[] = $path;
            }
        } else {
            //.
            $routeFiles = $this->scanRoutes();
        }

        $this->routeFiles = array_merge($this->routeFiles, $routeFiles);

        return $this;
    }

    private function scanRoutes(): array
    {
        $files = $this->getPhpFilesFromDirectory($this->basePath('routes'));

        return array_map(function (SplFileInfo $file) {
            return $file->getPathname();
        }, $files);
    }

    public function hasCommand(string $commandClassName): self
    {
        return $this->hasCommands([$commandClassName]);
    }

    public function hasCommands(array $commandClassNames): self
    {
        $this->commands = array_merge(
            $this->commands,
            collect($commandClassNames)->flatten()->toArray()
        );

        return $this;
    }

    public function hasViewComponent(string $prefix, string $viewComponentName): self
    {
        return $this->hasViewComponents($prefix, [$viewComponentName]);
    }

    public function hasViewComponents(string $prefix, array $viewComponentNames): self
    {
        foreach ($viewComponentNames as $componentName) {
            $this->viewComponents[$componentName] = $prefix;
        }

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
        return $this->hasServiceProviders([$serviceProvider]);
    }

    public function hasServiceProviders(array $serviceProviders): self
    {
        $this->serviceProviders = array_merge(
            $this->serviceProviders,
            $serviceProviders
        );

        return $this;
    }

    public function basePath(string $directory = null): string
    {
        $ds = DIRECTORY_SEPARATOR;

        return $directory === null
            ? $this->basePath
            : $this->basePath.$ds . ltrim($directory, $ds);
    }

    public function setBasePath(string $path): self
    {
        $this->basePath = $path;

        return $this;
    }

    private function getPhpFilesFromDirectory($path): array
    {
        $files = File::allFiles($path);

        return array_filter($files, function ($file) {
            return preg_match('/\.php/', $file->getBaseName());
        });
    }
}
