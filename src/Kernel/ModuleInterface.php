<?php

declare(strict_types=1);

namespace App\Kernel;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

/**
 * Interface for application modules.
 *
 * Each module can register its own services, routes, and configuration.
 */
interface ModuleInterface
{
    /**
     * Returns the module name (e.g., "User", "Order", "Shared").
     */
    public function getName(): string;

    /**
     * Returns the module root directory path.
     */
    public function getPath(): string;

    /**
     * Returns list of config files to load (relative to Config directory).
     *
     * Example: ['services.yaml', 'doctrine.yaml']
     */
    public function getConfigFiles(): array;

    /**
     * Returns list of route files to load (relative to Config directory).
     *
     * Example: ['routes.yaml', 'api_routes.yaml']
     */
    public function getRouteFiles(): array;

    /**
     * Configure routes for this module.
     *
     * This is called when loading the routing configuration.
     * Default implementation loads files from getRouteFiles().
     *
     * Override only if you need programmatic routing.
     */
    public function configureRoutes(RoutingConfigurator $routes): void;

    /**
     * Build container extensions (optional).
     *
     * This is called during container compilation.
     * You can add compiler passes, modify definitions, etc.
     */
    public function build(ContainerBuilder $container): void;

    /**
     * Boot the module (optional).
     *
     * This is called when the kernel boots.
     * Use for runtime initialization, event listeners, etc.
     */
    public function boot(): void;
}
