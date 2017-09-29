<?php

/*
 * (c) Christian Gripp <mail@core23.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Core23\FacebookBundle\Connection;

final class Session implements SessionInterface
{
    /**
     * @var string
     */
    private $fbid;

    /**
     * @var string
     */
    private $token;

    /**
     * @var \DateTime
     */
    private $expires;

    /**
     * Session constructor.
     *
     * @param string         $fbid
     * @param string         $token
     * @param \DateTime|null $expires
     */
    public function __construct(string $fbid, string $token, \DateTime $expires = null)
    {
        $this->fbid    = $fbid;
        $this->token   = $token;
        $this->expires = $expires;
    }

    /**
     * {@inheritdoc}
     */
    public function getFbid(): string
    {
        return $this->fbid;
    }

    /**
     * {@inheritdoc}
     */
    public function getToken(): string
    {
        return $this->token;
    }

    /**
     * {@inheritdoc}
     */
    public function getExpires(): ? \DateTime
    {
        return $this->expires;
    }
}
