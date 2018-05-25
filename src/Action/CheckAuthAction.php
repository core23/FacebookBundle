<?php

declare(strict_types=1);

/*
 * (c) Christian Gripp <mail@core23.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Core23\FacebookBundle\Action;

use Core23\FacebookBundle\Connection\FacebookConnection;
use Core23\FacebookBundle\Session\Session;
use Core23\FacebookBundle\Session\SessionManager;
use Facebook\Exceptions\FacebookSDKException;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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
     * @var FacebookConnection
     */
    private $facebookConnection;

    /**
     * @var SessionManager
     */
    private $sessionManager;

    /**
     * CheckAuthAction constructor.
     *
     * @param RouterInterface    $router
     * @param FacebookConnection $facebookConnection
     * @param SessionManager     $sessionManager
     */
    public function __construct(
        RouterInterface $router,
        FacebookConnection $facebookConnection,
        SessionManager $sessionManager
    ) {
        $this->router             = $router;
        $this->facebookConnection = $facebookConnection;
        $this->sessionManager     = $sessionManager;
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function __invoke(Request $request): Response
    {
        $fb     = $this->facebookConnection;
        $helper = $fb->getRedirectLoginHelper();

        try {
            if ($token = $helper->getAccessToken()) {
                $response = $fb->get('/me?fields=id,name', $token);

                $this->sessionManager->store(Session::fromFacebookApi($token, $response->getGraphUser()));

                return $this->redirectToRoute('core23_facebook_success');
            }
        } catch (FacebookSDKException $exception) {
            $this->logger->warning(sprintf('Facebook SDK Exception: %s', $exception->getMessage()));
        }

        return $this->redirectToRoute('core23_facebook_error');
    }

    /**
     * Returns a RedirectResponse to the given route with the given parameters.
     *
     * @param string $route      The name of the route
     * @param array  $parameters An array of parameters
     * @param int    $status     The status code to use for the Response
     *
     * @return RedirectResponse
     */
    private function redirectToRoute($route, array $parameters = [], $status = 302): RedirectResponse
    {
        return new RedirectResponse($this->generateUrl($route, $parameters), $status);
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
    private function generateUrl($route, array $parameters = [], $referenceType = UrlGeneratorInterface::ABSOLUTE_PATH): string
    {
        return $this->router->generate($route, $parameters, $referenceType);
    }
}
