<?php

declare(strict_types=1);

/*
 * (c) Christian Gripp <mail@core23.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Core23\FacebookBundle\Action;

use Facebook\Facebook;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;

final class StartAuthAction
{
    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var Facebook
     */
    private $facebookConnection;

    /**
     * @var string[]
     */
    private $permissions;

    /**
     * @param string[] $permissions
     */
    public function __construct(RouterInterface $router, Facebook $facebookConnection, array $permissions)
    {
        $this->router             = $router;
        $this->facebookConnection = $facebookConnection;
        $this->permissions        = $permissions;
    }

    public function __invoke(): RedirectResponse
    {
        $fb     = $this->facebookConnection;
        $helper = $fb->getRedirectLoginHelper();

        return new RedirectResponse($helper->getLoginUrl(
            $this->router->generate('core23_facebook_check', [], UrlGeneratorInterface::ABSOLUTE_URL),
            $this->permissions
        ));
    }
}
