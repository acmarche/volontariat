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

    public function loadExtension(array $config, ContainerConfigurator $container, ContainerBuilder $builder): void
    {
        $container->import('../config/services.php');
    }

    public function prependExtension(ContainerConfigurator $container, ContainerBuilder $builder): void
    {
        $container->import('../config/packages/doctrine.php');
        $container->import('../config/packages/imagine.php');
        $container->import('../config/packages/routing.php');
        $container->import('../config/packages/security.php');
        $container->import('../config/packages/vich_uploader.php');
        $container->import('../config/packages/twig.php');
    }
}
