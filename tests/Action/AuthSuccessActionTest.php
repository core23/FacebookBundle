<?php

/*
 * (c) Christian Gripp <mail@core23.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Core23\FacebookBundle\Tests\Action;

use Core23\FacebookBundle\Action\AuthSuccessAction;
use Core23\FacebookBundle\Core23FacebookEvents;
use Core23\FacebookBundle\Event\AuthSuccessEvent;
use Core23\FacebookBundle\Session\SessionInterface;
use Core23\FacebookBundle\Session\SessionManagerInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
use Twig\Environment;

class AuthSuccessActionTest extends TestCase
{
    private $twig;

    private $router;

    private $sessionManager;

    private $eventDispatcher;

    protected function setUp(): void
    {
        $this->twig            = $this->prophesize(Environment::class);
        $this->router          = $this->prophesize(RouterInterface::class);
        $this->sessionManager  = $this->prophesize(SessionManagerInterface::class);
        $this->eventDispatcher = $this->prophesize(EventDispatcherInterface::class);
    }

    public function testExecute(): void
    {
        $session = $this->prophesize(SessionInterface::class);

        $this->sessionManager->isAuthenticated()
            ->willReturn(true)
        ;
        $this->sessionManager->getSession()
            ->willReturn($session)
        ;
        $this->sessionManager->getUsername()
            ->willReturn('FooUser')
        ;

        $this->eventDispatcher->dispatch(Core23FacebookEvents::AUTH_SUCCESS, Argument::type(AuthSuccessEvent::class))
            ->shouldBeCalled()
        ;

        $this->twig->render('@Core23Facebook/Auth/success.html.twig', [
            'name' => 'FooUser',
        ])->shouldBeCalled();

        $action = new AuthSuccessAction(
            $this->twig->reveal(),
            $this->router->reveal(),
            $this->sessionManager->reveal(),
            $this->eventDispatcher->reveal()
        );

        $response = $action();

        $this->assertNotInstanceOf(RedirectResponse::class, $response);
        $this->assertSame(200, $response->getStatusCode());
    }

    public function testExecuteWithCaughtEvent(): void
    {
        $session = $this->prophesize(SessionInterface::class);

        $this->sessionManager->isAuthenticated()
            ->willReturn(true)
        ;
        $this->sessionManager->getSession()
            ->willReturn($session)
        ;
        $this->sessionManager->getUsername()
            ->willReturn('FooUser')
        ;

        $eventResponse = new Response();

        $this->eventDispatcher->dispatch(Core23FacebookEvents::AUTH_SUCCESS, Argument::type(AuthSuccessEvent::class))
            ->will(function ($args) use ($eventResponse) {
                $args[1]->setResponse($eventResponse);
            })
        ;

        $action = new AuthSuccessAction(
            $this->twig->reveal(),
            $this->router->reveal(),
            $this->sessionManager->reveal(),
            $this->eventDispatcher->reveal()
        );

        $response = $action();

        $this->assertSame($eventResponse, $response);
    }

    public function testExecuteNoAuth(): void
    {
        $this->sessionManager->isAuthenticated()
            ->willReturn(false)
        ;

        $this->router->generate('core23_facebook_error', [], UrlGeneratorInterface::ABSOLUTE_PATH)
            ->willReturn('/success')
            ->shouldBeCalled()
        ;

        $action = new AuthSuccessAction(
            $this->twig->reveal(),
            $this->router->reveal(),
            $this->sessionManager->reveal(),
            $this->eventDispatcher->reveal()
        );

        $this->assertInstanceOf(RedirectResponse::class, $action());
    }

    public function testExecuteNoSession(): void
    {
        $this->sessionManager->isAuthenticated()
            ->willReturn(true)
        ;
        $this->sessionManager->getSession()
            ->willReturn(null)
        ;

        $this->router->generate('core23_facebook_error', [], UrlGeneratorInterface::ABSOLUTE_PATH)
            ->willReturn('/success')
            ->shouldBeCalled()
        ;

        $action = new AuthSuccessAction(
            $this->twig->reveal(),
            $this->router->reveal(),
            $this->sessionManager->reveal(),
            $this->eventDispatcher->reveal()
        );

        $this->assertInstanceOf(RedirectResponse::class, $action());
    }
}