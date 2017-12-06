<?php

declare(strict_types=1);

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
    public function testLoadDefault(): void
    {
        $this->setParameter('kernel.bundles', []);
        $this->load([
            'api' => [
                'app_id'     => 'foo_id',
                'app_secret' => 'bar_secret',
            ],
        ]);

        $this->assertContainerBuilderHasParameter('core23.facebook.auth_success.redirect_route');
        $this->assertContainerBuilderHasParameter('core23.facebook.auth_success.redirect_route_params', []);
        $this->assertContainerBuilderHasParameter('core23.facebook.auth_error.redirect_route');
        $this->assertContainerBuilderHasParameter('core23.facebook.auth_error.redirect_route_params', []);

        $this->assertContainerBuilderHasParameter('core23.facebook.api.app_id', 'foo_id');
        $this->assertContainerBuilderHasParameter('core23.facebook.api.app_secret', 'bar_secret');
        $this->assertContainerBuilderHasParameter('core23.facebook.api.permissions', ['public_profile', 'user_likes']);
    }

    protected function getContainerExtensions(): array
    {
        return [
            new Core23FacebookExtension(),
        ];
    }
}
