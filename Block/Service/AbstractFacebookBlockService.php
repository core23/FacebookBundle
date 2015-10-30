<?php

/*
 * (c) Christian Gripp <mail@core23.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Core23\FacebookBundle\Block\Service;

use Core23\FacebookBundle\Connection\FacebookConnection;
use Sonata\BlockBundle\Block\Service\AbstractBlockService;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;

class AbstractFacebookBlockService extends AbstractBlockService
{
    /**
     * @var FacebookConnection
     */
    protected $connection;

    /**
     * @param string             $name
     * @param EngineInterface    $templating
     * @param FacebookConnection $connection
     */
    public function __construct($name, EngineInterface $templating, FacebookConnection $connection)
    {
        parent::__construct($name, $templating);

        $this->connection = $connection;
    }
}
