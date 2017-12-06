<?php

declare(strict_types=1);

/*
 * (c) Christian Gripp <mail@core23.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Core23\FacebookBundle\Block\Service;

use Facebook\Authentication\AccessToken;
use Facebook\Facebook;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\NullLogger;
use Sonata\BlockBundle\Block\Service\AbstractAdminBlockService;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;

abstract class AbstractFacebookBlockService extends AbstractAdminBlockService implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    /**
     * @var Facebook
     */
    protected $facebook;

    /**
     * @param string          $name
     * @param EngineInterface $templating
     * @param Facebook        $connection
     */
    public function __construct(string $name, EngineInterface $templating, Facebook $connection)
    {
        parent::__construct($name, $templating);

        $this->facebook = $connection;
        $this->logger   = new NullLogger();
    }

    /**
     * @return AccessToken
     */
    final protected function getAccessToken(): AccessToken
    {
        return $this->facebook->getApp()->getAccessToken();
    }
}
