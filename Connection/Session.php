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
     * Session constructor.
     *
     * @param string $fbid
     * @param string $token
     */
    public function __construct($fbid, $token)
    {
        $this->fbid  = $fbid;
        $this->token = $token;
    }

    /**
     * @return string
     */
    public function getFbid()
    {
        return $this->fbid;
    }

    /**
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }
}
