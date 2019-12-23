<?php

declare(strict_types=1);

/*
 * (c) Christian Gripp <mail@core23.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Core23\FacebookBundle\Action;

use Core23\FacebookBundle\Core23FacebookEvents;
use Core23\FacebookBundle\Event\AuthFailedEvent;
use Core23\FacebookBundle\Session\SessionManagerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

final class AuthErrorAction
{
    /**
     * @var Environment
     */
    private $twig;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var SessionManagerInterface
     */
    private $sessionManager;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    public function __construct(
        Environment $twig,
        RouterInterface $router,
        SessionManagerInterface $sessionManager,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->twig                = $twig;
        $this->router              = $router;
        $this->sessionManager      = $sessionManager;
        $this->eventDispatcher     = $eventDispatcher;
    }

    /**
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function __invoke(): Response
    {
        if ($this->sessionManager->isAuthenticated()) {
            return new RedirectResponse($this->router->generate('core23_facebook_success'));
        }

        $this->sessionManager->clear();

        $event = new AuthFailedEvent();
        $this->eventDispatcher->dispatch($event, Core23FacebookEvents::AUTH_ERROR);

        if (null !== $response = $event->getResponse()) {
            return $response;
        }

        return new Response($this->twig->render('@Core23Facebook/Auth/error.html.twig'), Response::HTTP_UNAUTHORIZED);
    }
}
