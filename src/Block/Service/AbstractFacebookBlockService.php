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
use Sonata\BlockBundle\Block\Service\AbstractBlockService;
use Sonata\BlockBundle\Block\Service\EditableBlockService;
use Sonata\BlockBundle\Form\Mapper\FormMapper;
use Sonata\BlockBundle\Model\BlockInterface;
use Sonata\Form\Validator\ErrorElement;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;

abstract class AbstractFacebookBlockService extends AbstractBlockService implements EditableBlockService, LoggerAwareInterface
{
    use LoggerAwareTrait;

    /**
     * @var Facebook
     */
    private $facebook;

    public function __construct(string $name, EngineInterface $templating, Facebook $connection)
    {
        parent::__construct($templating);

        $this->facebook = $connection;
        $this->logger   = new NullLogger();
    }

    public function configureCreateForm(FormMapper $form, BlockInterface $block): void
    {
        $this->configureEditForm($form, $block);
    }

    public function validate(ErrorElement $errorElement, BlockInterface $block): void
    {
    }

    public function getName(): string
    {
        return $this->getMetadata()->getTitle();
    }

    protected function getFacebook(): Facebook
    {
        return $this->facebook;
    }

    final protected function getAccessToken(): AccessToken
    {
        return $this->facebook->getApp()->getAccessToken();
    }
}
