<?php

declare(strict_types=1);

/*
 * (c) Christian Gripp <mail@core23.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Core23\FacebookBundle\Tests\DependencyInjection;

use Core23\FacebookBundle\DependencyInjection\Configuration;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Definition\Processor;

final class ConfigurationTest extends TestCase
{
    public function testOptions(): void
    {
        $processor = new Processor();

        $config = $processor->processConfiguration(new Configuration(), [[
            'api' => [
                'app_id'     => 'foo_id',
                'app_secret' => 'bar_secret',
            ],
        ]]);

        $expected = [
            'api' => [
                'app_id'      => 'foo_id',
                'app_secret'  => 'bar_secret',
                'permissions' => ['public_profile', 'user_likes'],
            ],
            'auth_success' => [
                'route'            => null,
                'route_parameters' => [],
            ],
            'auth_error' => [
                'route'            => null,
                'route_parameters' => [],
            ],
        ];

        $this->assertSame($expected, $config);
    }
}
