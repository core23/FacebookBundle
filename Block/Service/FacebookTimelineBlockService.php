<?php

/*
 * (c) Christian Gripp <mail@core23.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Core23\FacebookBundle\Block\Service;

use Core23\FacebookBundle\Connection\FacebookConnection;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\NullLogger;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\BlockBundle\Block\BlockContextInterface;
use Sonata\BlockBundle\Model\BlockInterface;
use Sonata\CoreBundle\Form\Type\ImmutableArrayType;
use Sonata\CoreBundle\Model\Metadata;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class FacebookTimelineBlockService extends AbstractFacebookBlockService implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    /**
     * @param string             $name
     * @param EngineInterface    $templating
     * @param FacebookConnection $connection
     */
    public function __construct($name, EngineInterface $templating, FacebookConnection $connection)
    {
        parent::__construct($name, $templating, $connection);

        $this->logger = new NullLogger();
    }

    /**
     * {@inheritdoc}
     */
    public function execute(BlockContextInterface $blockContext, Response $response = null)
    {
        $parameters = array(
            'context'  => $blockContext,
            'settings' => $blockContext->getSettings(),
            'block'    => $blockContext->getBlock(),
            // TODO
            'data' => $this->getData($blockContext->getSettings()),
        );

        return $this->renderResponse($blockContext->getTemplate(), $parameters, $response);
    }

    /**
     * {@inheritdoc}
     */
    public function buildEditForm(FormMapper $formMapper, BlockInterface $block)
    {
        $formMapper->add('settings', ImmutableArrayType::class, array(
            'keys' => array(
                array('title', TextType::class, array(
                    'label'    => 'form.label_title',
                    'required' => false,
                )),
                array('id', TextType::class, array(
                    'label'    => 'form.label_name',
                    'required' => false,
                )),
                array('class', TextType::class, array(
                    'label'    => 'form.label_class',
                    'required' => false,
                )),
            ),
            'translation_domain' => 'Core23FacebookBundle',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function configureSettings(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'title'    => 'Facebook Timeline',
            'id'       => null,
            'class'    => '',
            'template' => 'Core23FacebookBundle:Block:block_facebook_timeline.html.twig',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockMetadata($code = null)
    {
        return new Metadata($this->getName(), (!is_null($code) ? $code : $this->getName()), false, 'Core23FacebookBundle', array(
            'class' => 'fa fa-facebook',
        ));
    }
}
