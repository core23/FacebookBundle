<?php

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

class ConfigurationTest extends TestCase
{
    public function testOptions()
    {
        $processor = new Processor();

        $config = $processor->processConfiguration(new Configuration(), array(array(
            'api' => array(
                'app_id'     => 'foo_id',
                'app_secret' => 'bar_secret',
            ),
        )));

        $expected = array(
            'api' => array(
                'app_id'      => 'foo_id',
                'app_secret'  => 'bar_secret',
                'permissions' => array('public_profile', 'user_likes'),
            ),
            'auth_success' => array(
                'route'            => null,
                'route_parameters' => array(),
            ),
            'auth_error' => array(
                'route'            => null,
                'route_parameters' => array(),
            ),
        );

        $this->assertSame($expected, $config);
    }
}
