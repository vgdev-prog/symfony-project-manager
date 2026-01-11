<?php

declare(strict_types=1);

namespace App\Kernel;

use App\Shared\SharedModule;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

class Kernel extends BaseKernel
{
    use MicroKernelTrait;

    /**
     * @var ModuleInterface[]
     */
    private array $modules = [];

    /**
     * Register application modules.
     *
     * Order matters: later modules can override earlier ones.
     *
     * @return ModuleInterface[]
     */
    private function registerModules(): array
    {
        return [
            new SharedModule(),
            new \App\User\UserModule(),
        ];
    }

    /**
     * Get registered modules.
     *
     * @return ModuleInterface[]
     */
    public function getModules(): array
    {
        if (empty($this->modules)) {
            $this->modules = $this->registerModules();
        }

        return $this->modules;
    }

    /**
     * Configure container - load module configurations.
     *
     * IMPORTANT: MicroKernelTrait expects ONLY ContainerConfigurator parameter!
     */
    protected function configureContainer(ContainerConfigurator $container): void
    {
        // Load Symfony packages
        $container->import('../../config/{packages}/*.yaml');
        $container->import('../../config/{packages}/' . $this->environment . '/*.yaml');

        // Load main services.yaml
        if (file_exists(\dirname(__DIR__, 2) . '/config/services.yaml')) {
            $container->import('../../config/services.yaml');
        }

        // Load module configurations
        foreach ($this->getModules() as $module) {
            foreach ($module->getConfigFiles() as $configFile) {
                $file = $module->getPath() . '/Infrastructure/config/' . $configFile;

                if (file_exists($file)) {
                    $container->import($file);
                }
            }
        }
    }

    /**
     * Configure routes - load module routes.
     */
    protected function configureRoutes(RoutingConfigurator $routes): void
    {
        // Load Symfony package routes
        $routes->import('../../config/{routes}/' . $this->environment . '/*.yaml');
        $routes->import('../../config/{routes}/*.yaml');

        // Load main routes.yaml (optional - for global routes)
        if (file_exists(\dirname(__DIR__, 2) . '/config/routes.yaml')) {
            $routes->import('../../config/routes.yaml');
        }

        // Load module routes
        foreach ($this->getModules() as $module) {
            $module->configureRoutes($routes);
        }
    }

    /**
     * Build container - call module build methods.
     */
    protected function build(ContainerBuilder $container): void
    {
        foreach ($this->getModules() as $module) {
            $module->build($container);
        }
    }

    /**
     * Boot kernel - boot modules.
     */
    public function boot(): void
    {
        parent::boot();

        foreach ($this->getModules() as $module) {
            $module->boot();
        }
    }
}
