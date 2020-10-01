<?php

namespace AcMarche\Volontariat\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * This is the class that loads and manages your bundle configuration.
 *
 * @see https://symfony.com/doc/bundles/prepend_extension.html
 */
class VolontariatExtension extends Extension implements PrependExtensionInterface
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $containerBuilder): void
    {
        $phpFileLoader = new PhpFileLoader($containerBuilder, new FileLocator(__DIR__.'/../../config'));

        $phpFileLoader->load('services.php');
    }

    /**
     * Allow an extension to prepend the extension configurations.
     */
    public function prepend(ContainerBuilder $containerBuilder): void
    {
        $bundles = $containerBuilder->getParameter('kernel.bundles');

        if (isset($bundles['DoctrineBundle'])) {
            foreach (array_keys($containerBuilder->getExtensions()) as $name) {
                switch ($name) {
                    case 'doctrine':
                        $this->loadConfig($containerBuilder, 'doctrine');
                        break;
                    case 'twig':
                        $this->loadConfig($containerBuilder, 'twig');
                        break;
                    case 'framework':
                        $this->loadConfig($containerBuilder, 'security');
                        break;
                    case 'liip_imagine':
                        $this->loadConfig($containerBuilder, 'imagine');
                        break;
                    case 'vich_uploader':
                        $this->loadConfig($containerBuilder, 'vich_uploader');
                        break;
                }
            }
        }
    }

    protected function loadConfig(ContainerBuilder $containerBuilder, string $name): void
    {
        $configs = $this->loadYamlFile($containerBuilder);

        $configs->load($name.'.php');
        //  $container->prependExtensionConfig('doctrine', $configs);
    }

    protected function loadYamlFile(ContainerBuilder $containerBuilder): PhpFileLoader
    {
        return new PhpFileLoader(
            $containerBuilder,
            new FileLocator(__DIR__.'/../../config/packages/')
        );
    }
}
