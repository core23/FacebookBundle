<?php

declare(strict_types=1);

/*
 * (c) Christian Gripp <mail@core23.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Core23\FacebookBundle\Session;

use DateTime;

interface SessionInterface
{
    public function getFacebookId(): string;

    public function getName(): string;

    public function getToken(): string;

    public function getExpireDate(): ?DateTime;
}
