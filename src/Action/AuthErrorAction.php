<?php

declare(strict_types=1);

/*
 * (c) Christian Gripp <mail@core23.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Core23\FacebookBundle\Action;

use Core23\FacebookBundle\Session\SessionManager;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
use Twig\Environment;

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
     * @var SessionManager
     */
    private $sessionManager;

    /**
     * @var string|null
     */
    private $redirectRoute;

    /**
     * @var array
     */
    private $redirectRouteParams;

    /**
     * AuthErrorAction constructor.
     *
     * @param Environment     $twig
     * @param RouterInterface $router
     * @param SessionManager  $sessionManager
     * @param null|string     $redirectRoute
     * @param array           $redirectRouteParams
     */
    public function __construct(
        Environment $twig,
        RouterInterface $router,
        SessionManager $sessionManager,
        ?string $redirectRoute,
        array $redirectRouteParams
    ) {
        $this->twig                = $twig;
        $this->router              = $router;
        $this->sessionManager      = $sessionManager;
        $this->redirectRoute       = $redirectRoute;
        $this->redirectRouteParams = $redirectRouteParams;
    }

    /**
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     *
     * @return Response
     */
    public function __invoke(): Response
    {
        if ($this->sessionManager->isAuthenticated()) {
            return $this->redirectToRoute('core23_facebook_success');
        }

        if (null !== $this->redirectRoute) {
            return $this->redirectToRoute($this->redirectRoute, $this->redirectRouteParams);
        }

        return new Response($this->twig->render('@Core23Facebook/Auth/error.html.twig'));
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
