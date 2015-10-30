<?php

/*
 * (c) Christian Gripp <mail@core23.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Core23\FacebookBundle\Connection;

use Facebook\Facebook;

final class FacebookConnection extends Facebook
{
    /**
     * AbstractConnection constructor.
     *
     * @param string $apiKey
     * @param string $sharedSecret
     */
    public function __construct($apiKey, $sharedSecret)
    {
        parent::__construct(array(
            'app_id'     => $apiKey,
            'app_secret' => $sharedSecret,
        ));
    }
}
