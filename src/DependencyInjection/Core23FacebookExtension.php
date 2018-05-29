<?php

declare(strict_types=1);

/*
 * (c) Christian Gripp <mail@core23.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Core23\FacebookBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

final class Core23FacebookExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = new Configuration();
        $config        = $this->processConfiguration($configuration, $configs);
        $bundles       = $container->getParameter('kernel.bundles');

        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('action.xml');
        $loader->load('services.xml');

        if (isset($bundles['SonataBlockBundle'])) {
            $loader->load('block.xml');
        }

        $this->configureApi($container, $config);
    }

    /**
     * @param ContainerBuilder $container
     * @param array            $config
     */
    private function configureApi(ContainerBuilder $container, array $config): void
    {
        $container->setParameter('core23_facebook.api.app_id', $config['api']['app_id']);
        $container->setParameter('core23_facebook.api.app_secret', $config['api']['app_secret']);
        $container->setParameter('core23_facebook.api.permissions', $config['api']['permissions']);
    }
}
