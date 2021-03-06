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
     */
    public function isAuthenticated(): bool;

    /**
     * Get the session username.
     */
    public function getUsername(): ?string;

    public function store(SessionInterface $session): void;

    /**
     * Removes all stored sessions.
     */
    public function clear(): void;

    public function getSession(): ?SessionInterface;
}
