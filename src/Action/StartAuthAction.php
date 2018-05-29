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
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;

final class StartAuthAction
{
    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var FacebookConnection
     */
    private $facebookConnection;

    /**
     * @var string[]
     */
    private $permissions;

    /**
     * StartAuthAction constructor.
     *
     * @param RouterInterface    $router
     * @param FacebookConnection $facebookConnection
     * @param string[]           $permissions
     */
    public function __construct(RouterInterface $router, FacebookConnection $facebookConnection, array $permissions)
    {
        $this->router             = $router;
        $this->facebookConnection = $facebookConnection;
        $this->permissions        = $permissions;
    }

    /**
     * @return Response
     */
    public function __invoke(): Response
    {
        $fb     = $this->facebookConnection;
        $helper = $fb->getRedirectLoginHelper();

        return new RedirectResponse($helper->getLoginUrl(
            $this->generateUrl('core23_facebook_check', [], UrlGeneratorInterface::ABSOLUTE_URL),
            $this->permissions
        ));
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