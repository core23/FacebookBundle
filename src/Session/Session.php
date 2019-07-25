<?php

declare(strict_types=1);

/*
 * (c) Christian Gripp <mail@core23.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Core23\FacebookBundle\Session;

use Facebook\Authentication\AccessToken;
use Facebook\GraphNodes\GraphUser;

final class Session implements SessionInterface
{
    /**
     * @var string
     */
    private $facebookId;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $token;

    /**
     * @var \DateTime|null
     */
    private $expireDate;

    public function __construct(string $facebookId, string $name, string $token, ?\DateTime $expireDate)
    {
        $this->facebookId = $facebookId;
        $this->name       = $name;
        $this->token      = $token;
        $this->expireDate = $expireDate;
    }

    /**
     * {@inheritdoc}
     */
    public function getFacebookId(): string
    {
        return $this->facebookId;
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return $this->name;
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
    public function getExpireDate(): ?\DateTime
    {
        return $this->expireDate;
    }

    public static function fromFacebookApi(AccessToken $token, GraphUser $graphUser): SessionInterface
    {
        return new self($graphUser->getId() ?: '', $graphUser->getName() ?: '', $token->getValue(), $token->getExpiresAt());
    }
}
