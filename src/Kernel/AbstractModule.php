<?php

declare(strict_types=1);

namespace App\Kernel;

use ReflectionClass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

/**
 * Base class for application modules.
 *
 * Provides default implementations for common module operations.
 */
abstract class AbstractModule implements ModuleInterface
{
    /**
     * Get module name from class name.
     * Example: UserModule -> "User"
     */
    public function getName(): string
    {
        $className = (new ReflectionClass($this))->getShortName();
        return str_replace('Module', '', $className);
    }

    /**
     * Get module root directory.
     * Example: /path/to/src/User
     */
    public function getPath(): string
    {
        $reflection = new ReflectionClass($this);
        return dirname($reflection->getFileName());
    }

    /**
     * Get module config directory.
     * Example: /path/to/src/User/Infrastructure/Config
     */
    protected function getConfigPath(): string
    {
        return $this->getPath() . '/Infrastructure/config';
    }

    /**
     * Get list of config files to load.
     *
     * Default: ['services.yaml']
     *
     * Override this to specify which config files to load:
     *
     * ```php
     * protected function getConfigFiles(): array
     * {
     *     return [
     *         'services.yaml',
     *         'doctrine.yaml',
     *         'validation.yaml',
     *     ];
     * }
     * ```
     *
     * Environment-specific files are loaded automatically:
     *   - services.yaml (always)
     *   - services_dev.yaml (only in dev)
     *   - services_prod.yaml (only in prod)
     */
    public function getConfigFiles(): array
    {
        return [
            'services.yaml',  // Default: always load services.yaml
        ];
    }

    /**
     * Get list of route files to load.
     *
     * Default: ['routes.yaml'] (if exists)
     *
     * Override this to specify which route files to load:
     *
     * ```php
     * protected function getRouteFiles(): array
     * {
     *     return [
     *         'routes.yaml',
     *         'api_routes.yaml',
     *     ];
     * }
     * ```
     */
    public function getRouteFiles(): array
    {
        return [
            'routes.yaml',  // Default: load routes.yaml if exists
        ];
    }

    /**
     * Configure routes from route files.
     *
     * Default implementation loads files from getRouteFiles().
     *
     * Override only if you need programmatic routing.
     */
    public function configureRoutes(RoutingConfigurator $routes): void
    {
        $configPath = $this->getConfigPath();

        // Load each route file
        foreach ($this->getRouteFiles() as $routeFile) {
            $file = $configPath . '/' . $routeFile;

            if (file_exists($file)) {
                $routes->import($file);
            }
        }
    }

    /**
     * Build container (default: do nothing).
     *
     * Override this to add compiler passes or modify container.
     *
     * Example:
     * ```php
     * public function build(ContainerBuilder $container): void
     * {
     *     parent::build($container);
     *     $container->addCompilerPass(new MyCustomPass());
     * }
     * ```
     */
    public function build(ContainerBuilder $container): void
    {
        // Override in subclass if needed
    }

    /**
     * Boot module (default: do nothing).
     *
     * Override this to perform runtime initialization.
     *
     * Example:
     * ```php
     * public function boot(): void
     * {
     *     Type::addType('email', EmailType::class);
     * }
     * ```
     */
    public function boot(): void
    {
        // Override in subclass if needed
    }
}
