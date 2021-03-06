<?php

declare(strict_types=1);

/*
 * (c) Christian Gripp <mail@core23.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Core23\FacebookBundle\Connection;

use Facebook\Exceptions\FacebookSDKException;
use Facebook\Facebook;

final class FacebookConnection extends Facebook
{
    /**
     * @var string
     */
    private $apiId;

    /**
     * @var string
     */
    private $sharedSecret;

    /**
     * @throws FacebookSDKException
     */
    public function __construct(string $apiKey, string $sharedSecret)
    {
        parent::__construct([
            'app_id'     => $apiKey,
            'app_secret' => $sharedSecret,
        ]);

        $this->apiId        = $apiKey;
        $this->sharedSecret = $sharedSecret;
    }

    public function getApiId(): string
    {
        return $this->apiId;
    }

    public function getSharedSecret(): string
    {
        return $this->sharedSecret;
    }
}
