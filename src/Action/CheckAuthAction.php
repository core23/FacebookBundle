<?php

declare(strict_types=1);

/*
 * (c) Christian Gripp <mail@core23.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Core23\FacebookBundle\Action;

use Core23\FacebookBundle\Session\Session;
use Core23\FacebookBundle\Session\SessionInterface;
use Core23\FacebookBundle\Session\SessionManagerInterface;
use Facebook\Exceptions\FacebookSDKException;
use Facebook\Facebook;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\NullLogger;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\RouterInterface;

final class CheckAuthAction implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var Facebook
     */
    private $facebookConnection;

    /**
     * @var SessionManagerInterface
     */
    private $sessionManager;

    public function __construct(
        RouterInterface $router,
        Facebook $facebookConnection,
        SessionManagerInterface $sessionManager
    ) {
        $this->router             = $router;
        $this->facebookConnection = $facebookConnection;
        $this->sessionManager     = $sessionManager;
        $this->logger             = new NullLogger();
    }

    public function __invoke(): RedirectResponse
    {
        $session = $this->getSession();

        if (null !== $session) {
            $this->sessionManager->store($session);

            return new RedirectResponse($this->router->generate('core23_facebook_success'));
        }

        return new RedirectResponse($this->router->generate('core23_facebook_error'));
    }

    private function getSession(): ?SessionInterface
    {
        $fb     = $this->facebookConnection;
        $helper = $fb->getRedirectLoginHelper();
        $token  = $helper->getAccessToken();

        if (null === $token) {
            return null;
        }

        try {
            $response = $fb->get('/me?fields=id,name', $token);

            return Session::fromFacebookApi($token, $response->getGraphUser());
        } catch (FacebookSDKException $exception) {
            $this->logger->warning(sprintf('Facebook SDK Exception: %s', $exception->getMessage()));
        }

        return null;
    }
}
