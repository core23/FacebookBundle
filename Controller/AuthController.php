<?php

/*
 * (c) Christian Gripp <mail@core23.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Core23\FacebookBundle\Controller;

use Core23\FacebookBundle\Connection\FacebookConnection;
use Facebook\Exceptions\FacebookSDKException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class AuthController extends Controller
{
    const SESSION_FB_ID      = '_CORE23_FACEBOOK_ID';
    const SESSION_FB_NAME    = '_CORE23_FACEBOOK_NAME';
    const SESSION_FB_TOKEN   = '_CORE23_FACEBOOK_TOKEN';
    const SESSION_FB_EXPIRES = '_CORE23_FACEBOOK_EXPIRES';

    /**
     * @return Response
     */
    public function authAction(): Response
    {
        $fb     = $this->getFacebookConnection();
        $helper = $fb->getRedirectLoginHelper();

        return $this->redirect($helper->getLoginUrl(
            $this->generateUrl('core23_facebook_check', array(), UrlGeneratorInterface::ABSOLUTE_URL),
            $this->getParameter('core23.facebook.api.permissions')
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
            $token = $helper->getAccessToken();

            $response = $fb->get('/me?fields=id,name', $token);
            $fbid     = $response->getGraphUser()->getId();
            $name     = $response->getGraphUser()->getName();

            /** @var Session $session */
            $session = $this->get('session');
            $session->set(static::SESSION_FB_ID, $fbid);
            $session->set(static::SESSION_FB_NAME, $name);
            $session->set(static::SESSION_FB_TOKEN, $token);
            $session->set(static::SESSION_FB_EXPIRES, $token->getExpiresAt());

            return $this->redirectToRoute('core23_facebook_success');
        } catch (FacebookSDKException $exception) {
            $this->get('logger')->warning(sprintf('Facebook SDK Exception: %s', $exception->getMessage()));
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

        if (null != $this->getParameter('core23.facebook.auth_error.redirect_route')) {
            return $this->redirectToRoute($this->getParameter('core23.facebook.auth_error.redirect_route'), $this->getParameter('core23.facebook.auth_error.redirect_route_params'));
        }

        return $this->render('Core23FacebookBundle:Auth:error.html.twig');
    }

    /**
     * @return Response
     */
    public function successAction(): Response
    {
        if (!$this->isAuthenticated()) {
            return $this->redirectToRoute('core23_facebook_error');
        }

        if (null != $this->getParameter('core23.facebook.auth_success.redirect_route')) {
            return $this->redirectToRoute($this->getParameter('core23.facebook.auth_success.redirect_route'), $this->getParameter('core23.facebook.auth_success.redirect_route_params'));
        }

        $session = $this->get('session');

        return $this->render('Core23FacebookBundle:Auth:success.html.twig', array(
            'name' => $session->get(static::SESSION_FB_NAME),
        ));
    }

    /**
     * Returns the auth status.
     *
     * @return bool
     */
    private function isAuthenticated(): bool
    {
        return (bool) $this->get('session')->get(static::SESSION_FB_TOKEN);
    }

    /**
     * @return FacebookConnection
     */
    private function getFacebookConnection(): FacebookConnection
    {
        return $this->get('core23.facebook.connection');
    }
}
