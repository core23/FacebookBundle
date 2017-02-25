<?php

/*
 * (c) Christian Gripp <mail@core23.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Core23\FacebookBundle\Block\Service;

use Facebook\Exceptions\FacebookSDKException;
use Psr\Log\LoggerAwareInterface;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\BlockBundle\Block\BlockContextInterface;
use Sonata\BlockBundle\Model\BlockInterface;
use Sonata\CoreBundle\Form\Type\ImmutableArrayType;
use Sonata\CoreBundle\Model\Metadata;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class PageFeedBlockService extends AbstractFacebookBlockService implements LoggerAwareInterface
{
    /**
     * {@inheritdoc}
     */
    public function execute(BlockContextInterface $blockContext, Response $response = null)
    {
        $parameters = array(
            'context'  => $blockContext,
            'settings' => $blockContext->getSettings(),
            'block'    => $blockContext->getBlock(),
            'feed'     => $this->getData($blockContext->getSettings()),
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
                    'label'    => 'form.label_id',
                    'required' => true,
                )),
                array('limit', NumberType::class, array(
                    'label'    => 'form.label_limit',
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
            'limit'    => 10,
            'class'    => '',
            'fields'   => 'type,message,description,permalink_url,picture,created_time',
            'template' => 'Core23FacebookBundle:Block:block_page_feed.html.twig',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockMetadata($code = null)
    {
        return new Metadata($this->getName(), (!is_null($code) ? $code : $this->getName()), false, 'Core23FacebookBundle', array(
            'class' => 'fa fa-facebook-official',
        ));
    }

    /**
     * @param array $settings
     *
     * @return array
     */
    private function getData(array $settings): array
    {
        try {
            $accessToken = $this->getAccessToken();

            $response = $this->facebook->get('/'.$settings['id'].'/feed?fields='.$settings['fields'], $accessToken);

            return $response->getGraphEdge()->asArray();
        } catch (FacebookSDKException $exception) {
            $this->logger->warning(sprintf('Facebook SDK Exception: %s', $exception->getMessage()));
        }
    }
}
