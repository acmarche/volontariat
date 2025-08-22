<?php

namespace AcMarche\Volontariat;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;

class AcMarcheVolontariatBundle extends AbstractBundle
{
    public function getPath(): string
    {
        return \dirname(__DIR__);
    }

    public function loadExtension(array $config, ContainerConfigurator $containerConfigurator, ContainerBuilder $containerBuilder): void
    {
        $containerConfigurator->import('../config/services.php');
    }

    public function prependExtension(ContainerConfigurator $containerConfigurator, ContainerBuilder $containerBuilder): void
    {
        $containerConfigurator->import('../config/packages/doctrine.php');
        $containerConfigurator->import('../config/packages/imagine.php');
        $containerConfigurator->import('../config/packages/rate_limiter.php');
        $containerConfigurator->import('../config/packages/routing.php');
        $containerConfigurator->import('../config/packages/security.php');
        $containerConfigurator->import('../config/packages/vich_uploader.php');
        $containerConfigurator->import('../config/packages/twig.php');
    }
}
