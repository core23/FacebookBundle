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
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
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

    /**
     * @param RouterInterface         $router
     * @param Facebook                $facebookConnection
     * @param SessionManagerInterface $sessionManager
     */
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

    /**
     * @return RedirectResponse
     */
    public function __invoke(): RedirectResponse
    {
        $session = $this->getSession();

        if (null !== $session) {
            $this->sessionManager->store($session);

            return new RedirectResponse($this->generateUrl('core23_facebook_success'));
        }

        return new RedirectResponse($this->generateUrl('core23_facebook_error'));
    }

    /**
     * Generates a URL from the given parameters.
     *
     * @param string $route         The name of the route
     * @param array  $parameters    An array of parameters
     * @param int    $referenceType The type of reference (one of the constants in UrlGeneratorInterface)
     *
     * @return string The generated URL
     */
    private function generateUrl(string $route, array $parameters = [], int $referenceType = UrlGeneratorInterface::ABSOLUTE_PATH): string
    {
        return $this->router->generate($route, $parameters, $referenceType);
    }

    /**
     * @return SessionInterface|null
     */
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
