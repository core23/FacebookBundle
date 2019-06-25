<?php

/*
 * (c) Christian Gripp <mail@core23.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Core23\FacebookBundle\Tests\Action;

use Core23\FacebookBundle\Action\CheckAuthAction;
use Core23\FacebookBundle\Session\SessionInterface;
use Core23\FacebookBundle\Session\SessionManagerInterface;
use DateTime;
use Facebook\Authentication\AccessToken;
use Facebook\Exceptions\FacebookSDKException;
use Facebook\Facebook;
use Facebook\FacebookResponse;
use Facebook\GraphNodes\GraphUser;
use Facebook\Helpers\FacebookRedirectLoginHelper;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;

final class CheckAuthActionTest extends TestCase
{
    private $router;

    private $facebook;

    private $sessionManager;

    private $helper;

    protected function setUp(): void
    {
        $this->router         = $this->prophesize(RouterInterface::class);
        $this->sessionManager = $this->prophesize(SessionManagerInterface::class);

        $this->helper = $this->prophesize(FacebookRedirectLoginHelper::class);

        $this->facebook = $this->prophesize(Facebook::class);
        $this->facebook->getRedirectLoginHelper()
            ->willReturn($this->helper)
        ;
    }

    public function testExecute(): void
    {
        $accessToken = $this->prepareAccessToken();

        $graphUser = $this->prophesize(GraphUser::class);

        $response = $this->prophesize(FacebookResponse::class);
        $response->getGraphUser()
            ->willReturn($graphUser)
        ;

        $this->facebook->get('/me?fields=id,name', $accessToken)
            ->willReturn($response)
        ;

        $this->sessionManager->store(Argument::type(SessionInterface::class))
            ->shouldBeCalled()
        ;

        $this->router->generate('core23_facebook_success', [], UrlGeneratorInterface::ABSOLUTE_PATH)
            ->willReturn('/success')
            ->shouldBeCalled()
        ;

        $action = new CheckAuthAction(
            $this->router->reveal(),
            $this->facebook->reveal(),
            $this->sessionManager->reveal()
        );

        $response = $action();

        static::assertSame('/success', $response->getTargetUrl());
    }

    public function testExecuteWithApiError(): void
    {
        $accessToken = $this->prepareAccessToken();

        $graphUser = $this->prophesize(GraphUser::class);

        $response = $this->prophesize(FacebookResponse::class);
        $response->getGraphUser()
            ->willReturn($graphUser)
        ;

        $this->facebook->get('/me?fields=id,name', $accessToken)
            ->willThrow(FacebookSDKException::class)
        ;

        $this->router->generate('core23_facebook_error', [], UrlGeneratorInterface::ABSOLUTE_PATH)
            ->willReturn('/error')
            ->shouldBeCalled()
        ;

        $action = new CheckAuthAction(
            $this->router->reveal(),
            $this->facebook->reveal(),
            $this->sessionManager->reveal()
        );

        $response = $action();

        static::assertSame('/error', $response->getTargetUrl());
    }

    public function testExecuteWithNoAccessToken(): void
    {
        $this->helper = $this->prophesize(FacebookRedirectLoginHelper::class);
        $this->helper->getAccessToken()
            ->willReturn(null)
        ;

        $this->router->generate('core23_facebook_error', [], UrlGeneratorInterface::ABSOLUTE_PATH)
            ->willReturn('/error')
            ->shouldBeCalled()
        ;

        $action = new CheckAuthAction(
            $this->router->reveal(),
            $this->facebook->reveal(),
            $this->sessionManager->reveal()
        );

        $response = $action();

        static::assertSame('/error', $response->getTargetUrl());
    }

    /**
     * @return ObjectProphecy
     */
    private function prepareAccessToken(): ObjectProphecy
    {
        $accessToken = $this->prophesize(AccessToken::class);
        $accessToken->getValue()
            ->willReturn('TOKEN_VALUE')
        ;
        $accessToken->getExpiresAt()
            ->willReturn(new DateTime('tomorrow'))
        ;

        $this->helper->getAccessToken()
            ->willReturn($accessToken)
        ;

        return $accessToken;
    }
}
