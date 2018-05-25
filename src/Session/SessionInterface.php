<?php

declare(strict_types=1);

/*
 * (c) Christian Gripp <mail@core23.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Core23\FacebookBundle\Session;

interface SessionInterface
{
    /**
     * @return string
     */
    public function getFacebookId(): string;

    /**
     * @return string
     */
    public function getName(): string;

    /**
     * @return string
     */
    public function getToken(): string;

    /**
     * @return \DateTime|null
     */
    public function getExpireDate(): ?\DateTime;
}
