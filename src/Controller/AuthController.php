<?php

declare(strict_types=1);

/*
 * (c) Christian Gripp <mail@core23.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Core23\FacebookBundle\Controller;

use Core23\FacebookBundle\Connection\FacebookConnection;
use Facebook\Authentication\AccessToken;
use Facebook\Exceptions\FacebookSDKException;
use Facebook\GraphNodes\GraphUser;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class AuthController extends Controller
{
    public const SESSION_FB_ID      = '_CORE23_FACEBOOK_ID';
    public const SESSION_FB_NAME    = '_CORE23_FACEBOOK_NAME';
    public const SESSION_FB_TOKEN   = '_CORE23_FACEBOOK_TOKEN';
    public const SESSION_FB_EXPIRES = '_CORE23_FACEBOOK_EXPIRES';

    /**
     * @return Response
     */
    public function authAction(): Response
    {
        $fb     = $this->getFacebookConnection();
        $helper = $fb->getRedirectLoginHelper();

        return $this->redirect($helper->getLoginUrl(
            $this->generateUrl('core23_facebook_check', [], UrlGeneratorInterface::ABSOLUTE_URL),
            $this->getParameter('core23_facebook.api.permissions')
        ));
    }

    /**
     * @return Response
     */
    public function checkAction(): Response
    {
        $fb     = $this->getFacebookConnection();
        $helper = $fb->getRedirectLoginHelper();

        try {
            if ($token = $helper->getAccessToken()) {
                $response = $fb->get('/me?fields=id,name', $token);

                $this->storeCredentials($token, $response->getGraphUser());

                return $this->redirectToRoute('core23_facebook_success');
            }
        } catch (FacebookSDKException $exception) {
            $this->getLogger()->warning(sprintf('Facebook SDK Exception: %s', $exception->getMessage()));
        }

        return $this->redirectToRoute('core23_facebook_error');
    }

    /**
     * @return Response
     */
    public function errorAction(): Response
    {
        if ($this->isAuthenticated()) {
            return $this->redirectToRoute('core23_facebook_success');
        }

        if (null !== $this->getParameter('core23_facebook.auth_error.redirect_route')) {
            return $this->redirectToRoute($this->getParameter('core23_facebook.auth_error.redirect_route'), $this->getParameter('core23_facebook.auth_error.redirect_route_params'));
        }

        return $this->render('@Core23Facebook/Auth/error.html.twig');
    }

    /**
     * @return Response
     */
    public function successAction(): Response
    {
        if (!$this->isAuthenticated()) {
            return $this->redirectToRoute('core23_facebook_error');
        }

        if (null !== $this->getParameter('core23_facebook.auth_success.redirect_route')) {
            return $this->redirectToRoute($this->getParameter('core23_facebook.auth_success.redirect_route'), $this->getParameter('core23_facebook.auth_success.redirect_route_params'));
        }

        $session = $this->getSession();

        return $this->render('@Core23Facebook/Auth/success.html.twig', [
            'name' => $session->get(static::SESSION_FB_NAME),
        ]);
    }

    /**
     * @param AccessToken $token
     * @param GraphUser   $user
     */
    private function storeCredentials(AccessToken $token, GraphUser $user): void
    {
        $fbid = $user->getId();
        $name = $user->getName();

        $session = $this->getSession();
        $session->set(static::SESSION_FB_ID, $fbid);
        $session->set(static::SESSION_FB_NAME, $name);
        $session->set(static::SESSION_FB_TOKEN, $token->getValue());
        $session->set(static::SESSION_FB_EXPIRES, $token->getExpiresAt());
    }

    /**
     * Returns the auth status.
     *
     * @return bool
     */
    private function isAuthenticated(): bool
    {
        return (bool) $this->getSession()->get(static::SESSION_FB_TOKEN);
    }

    /**
     * @return FacebookConnection
     */
    private function getFacebookConnection(): FacebookConnection
    {
        return $this->get('core23_facebook.connection');
    }

    /**
     * @return LoggerInterface
     */
    private function getLogger(): LoggerInterface
    {
        return $this->get('logger');
    }

    /**
     * @return SessionInterface
     */
    private function getSession(): SessionInterface
    {
        return $this->get('session');
    }
}
