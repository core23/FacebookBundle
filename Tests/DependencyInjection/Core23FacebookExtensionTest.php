<?php

/*
 * (c) Christian Gripp <mail@core23.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Core23\FacebookBundle\Tests\DependencyInjection;

use Core23\FacebookBundle\DependencyInjection\Core23FacebookExtension;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;

class Core23FacebookExtensionTest extends AbstractExtensionTestCase
{
    public function testLoadDefault()
    {
        $this->setParameter('kernel.bundles', array());
        $this->load(array(
            'api' => array(
                'app_id'     => 'foo_id',
                'app_secret' => 'bar_secret',
            ),
        ));

        $this->assertContainerBuilderHasParameter('core23.facebook.auth_success.redirect_route', null);
        $this->assertContainerBuilderHasParameter('core23.facebook.auth_success.redirect_route_params', array());
        $this->assertContainerBuilderHasParameter('core23.facebook.auth_error.redirect_route', null);
        $this->assertContainerBuilderHasParameter('core23.facebook.auth_error.redirect_route_params', array());

        $this->assertContainerBuilderHasParameter('core23.facebook.api.app_id', 'foo_id');
        $this->assertContainerBuilderHasParameter('core23.facebook.api.app_secret', 'bar_secret');
        $this->assertContainerBuilderHasParameter('core23.facebook.api.permissions', array('public_profile', 'user_likes'));
    }

    protected function getContainerExtensions()
    {
        return array(
            new Core23FacebookExtension(),
        );
    }
}
