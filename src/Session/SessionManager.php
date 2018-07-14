<?php

declare(strict_types=1);

/*
 * (c) Christian Gripp <mail@core23.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Core23\FacebookBundle\Session;

use Core23\FacebookBundle\Session\Session as FacebookSession;
use Symfony\Component\HttpFoundation\Session\Session;

final class SessionManager implements SessionManagerInterface
{
    private const SESSION_FB_EXPIRES = '_CORE23_FACEBOOK_EXPIRES';

    private const SESSION_FB_NAME    = '_CORE23_FACEBOOK_NAME';

    private const SESSION_FB_ID      = '_CORE23_FACEBOOK_ID';

    private const SESSION_FB_TOKEN   = '_CORE23_FACEBOOK_TOKEN';

    /**
     * @var Session
     */
    private $session;

    /**
     * @param Session $session
     */
    public function __construct(Session $session)
    {
        $this->session = $session;
    }

    /**
     * {@inheritdoc}
     */
    public function isAuthenticated(): bool
    {
        return (bool) $this->session->get(static::SESSION_FB_TOKEN);
    }

    /**
     * {@inheritdoc}
     */
    public function getUsername(): ?string
    {
        return $this->session->get(static::SESSION_FB_NAME);
    }

    /**
     * {@inheritdoc}
     */
    public function store(SessionInterface $session): void
    {
        $this->session->set(static::SESSION_FB_ID, $session->getFacebookId());
        $this->session->set(static::SESSION_FB_NAME, $session->getName());
        $this->session->set(static::SESSION_FB_TOKEN, $session->getToken());
        $this->session->set(static::SESSION_FB_EXPIRES, $session->getExpireDate());
    }

    /**
     * {@inheritdoc}
     */
    public function clear(): void
    {
        $this->session->remove(static::SESSION_FB_ID);
        $this->session->remove(static::SESSION_FB_NAME);
        $this->session->remove(static::SESSION_FB_TOKEN);
        $this->session->remove(static::SESSION_FB_EXPIRES);
    }

    /**
     * {@inheritdoc}
     */
    public function getSession(): ?SessionInterface
    {
        if (!$this->isAuthenticated()) {
            return null;
        }

        return new FacebookSession(
            $this->session->get(static::SESSION_FB_ID),
            $this->session->get(static::SESSION_FB_NAME),
            $this->session->get(static::SESSION_FB_TOKEN),
            $this->session->get(static::SESSION_FB_EXPIRES)
        );
    }
}
