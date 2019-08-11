<?php

declare(strict_types=1);

/*
 * (c) Christian Gripp <mail@core23.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Core23\FacebookBundle\Tests\DependencyInjection;

use Core23\FacebookBundle\Action\AuthErrorAction;
use Core23\FacebookBundle\Action\AuthSuccessAction;
use Core23\FacebookBundle\Action\CheckAuthAction;
use Core23\FacebookBundle\Action\StartAuthAction;
use Core23\FacebookBundle\Block\Service\PageFeedBlockService;
use Core23\FacebookBundle\Connection\FacebookConnection;
use Core23\FacebookBundle\DependencyInjection\Core23FacebookExtension;
use Core23\FacebookBundle\Session\SessionManager;
use Core23\FacebookBundle\Session\SessionManagerInterface;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;

final class Core23FacebookExtensionTest extends AbstractExtensionTestCase
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

        $this->assertContainerBuilderHasParameter('core23_facebook.api.app_id', 'foo_id');
        $this->assertContainerBuilderHasParameter('core23_facebook.api.app_secret', 'bar_secret');
        $this->assertContainerBuilderHasParameter('core23_facebook.api.permissions', ['public_profile', 'user_likes']);

        $this->assertContainerBuilderHasService(StartAuthAction::class);
        $this->assertContainerBuilderHasService(AuthErrorAction::class);
        $this->assertContainerBuilderHasService(AuthSuccessAction::class);
        $this->assertContainerBuilderHasService(CheckAuthAction::class);

        $this->assertContainerBuilderHasAlias(SessionManagerInterface::class, 'core23_facebook.session.manager');
        $this->assertContainerBuilderHasAlias(FacebookConnection::class, 'core23_facebook.connection');
        $this->assertContainerBuilderHasService('core23_facebook.session.manager', SessionManager::class);
        $this->assertContainerBuilderHasService('core23_facebook.connection', FacebookConnection::class);
    }

    public function testLoadWithBlockBundle(): void
    {
        $this->setParameter('kernel.bundles', ['SonataBlockBundle' => true]);
        $this->load([
            'api' => [
                'app_id'     => 'foo_id',
                'app_secret' => 'bar_secret',
            ],
        ]);

        $this->assertContainerBuilderHasService('core23_facebook.block.page_feed', PageFeedBlockService::class);
    }

    protected function getContainerExtensions(): array
    {
        return [
            new Core23FacebookExtension(),
        ];
    }
}
