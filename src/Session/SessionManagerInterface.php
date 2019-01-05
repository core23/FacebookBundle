<?php

declare(strict_types=1);

/*
 * (c) Christian Gripp <mail@core23.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Core23\FacebookBundle\Session;

interface SessionManagerInterface
{
    /**
     * Returns the auth status.
     *
     * @return bool
     */
    public function isAuthenticated(): bool;

    /**
     * Get the session username.
     *
     * @return string|null
     */
    public function getUsername(): ?string;

    /**
     * @param SessionInterface $session
     */
    public function store(SessionInterface $session): void;

    /**
     * Removes all stored sessions.
     */
    public function clear(): void;

    /**
     * @return SessionInterface|null
     */
    public function getSession(): ?SessionInterface;
}
