<?php

declare(strict_types=1);

/*
 * (c) Christian Gripp <mail@core23.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Core23\FacebookBundle;

final class Core23FacebookEvents
{
    public const AUTH_SUCCESS = 'core23_facebook.event.auth.success';
    public const AUTH_ERROR   = 'core23_facebook.event.auth.error';
}
