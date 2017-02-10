<?php

/*
 * (c) Christian Gripp <mail@core23.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Core23\FacebookBundle\Connection;

interface SessionInterface
{
    /**
     * Get fbid.
     *
     * @return string
     */
    public function getFbid(): string;

    /**
     * Get token.
     *
     * @return string
     */
    public function getToken(): string;
}
