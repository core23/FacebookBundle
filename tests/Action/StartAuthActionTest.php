<?php

/*
 * (c) Christian Gripp <mail@core23.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Core23\FacebookBundle\Tests\Action;

use Core23\FacebookBundle\Action\StartAuthAction;
use Facebook\Facebook;
use Facebook\Helpers\FacebookRedirectLoginHelper;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;

class StartAuthActionTest extends TestCase
{
    private $router;

    private $facebook;

    protected function setUp(): void
    {
        $this->router   = $this->prophesize(RouterInterface::class);
        $this->facebook = $this->prophesize(Facebook::class);
    }

    public function testExecute(): void
    {
        $helper = $this->prophesize(FacebookRedirectLoginHelper::class);
        $helper->getLoginUrl('/start', ['DUMMY_PERMISSION'])
            ->willReturn('https://facebook/login')
        ;

        $this->facebook->getRedirectLoginHelper()
            ->willReturn($helper)
        ;

        $this->router->generate('core23_facebook_check', [], UrlGeneratorInterface::ABSOLUTE_URL)
            ->willReturn('/start')
            ->shouldBeCalled()
        ;

        $action = new StartAuthAction(
            $this->router->reveal(),
            $this->facebook->reveal(),
            ['DUMMY_PERMISSION']
        );

        $this->assertSame('https://facebook/login', $action()->getTargetUrl());
    }
}
