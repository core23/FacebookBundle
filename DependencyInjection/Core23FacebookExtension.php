<?php

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
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config        = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        // TODO: $loader->load('block.xml');
        $loader->load('services.xml');

        $this->configureRoutes($container, $config);
        $this->configureApi($container, $config);
    }

    /**
     * @param ContainerBuilder $container
     * @param array            $config
     */
    private function configureRoutes(ContainerBuilder $container, array $config)
    {
        $container->setParameter('core23.facebook.auth_success.redirect_route', $config['auth_success']['route']);
        $container->setParameter('core23.facebook.auth_success.redirect_route_params', $config['auth_success']['route_parameters']);

        $container->setParameter('core23.facebook.auth_error.redirect_route', $config['auth_success']['route']);
        $container->setParameter('core23.facebook.auth_error.redirect_route_params', $config['auth_success']['route_parameters']);
    }

    /**
     * @param ContainerBuilder $container
     * @param array            $config
     */
    private function configureApi(ContainerBuilder $container, array $config)
    {
        $container->setParameter('core23.facebook.api.app_id', $config['api']['app_id']);
        $container->setParameter('core23.facebook.api.app_secret', $config['api']['app_secret']);
        $container->setParameter('core23.facebook.api.permissions', $config['api']['permissions']);
    }
}
