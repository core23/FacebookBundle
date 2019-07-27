<?php

declare(strict_types=1);

/*
 * (c) Christian Gripp <mail@core23.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Core23\FacebookBundle\Block\Service;

use Facebook\Exceptions\FacebookSDKException;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\BlockBundle\Block\BlockContextInterface;
use Sonata\BlockBundle\Meta\Metadata;
use Sonata\BlockBundle\Model\BlockInterface;
use Sonata\Form\Type\ImmutableArrayType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class PageFeedBlockService extends AbstractFacebookBlockService
{
    public function execute(BlockContextInterface $blockContext, Response $response = null)
    {
        $parameters = [
            'context'  => $blockContext,
            'settings' => $blockContext->getSettings(),
            'block'    => $blockContext->getBlock(),
            'feed'     => $this->getData($blockContext->getSettings()),
        ];

        return $this->renderResponse($blockContext->getTemplate(), $parameters, $response);
    }

    public function buildEditForm(FormMapper $formMapper, BlockInterface $block): void
    {
        $formMapper->add('settings', ImmutableArrayType::class, [
            'keys' => [
                ['title', TextType::class, [
                    'label'    => 'form.label_title',
                    'required' => false,
                ]],
                ['translation_domain', TextType::class, [
                    'label'    => 'form.label_translation_domain',
                    'required' => false,
                ]],
                ['icon', TextType::class, [
                    'label'    => 'form.label_icon',
                    'required' => false,
                ]],
                ['class', TextType::class, [
                    'label'    => 'form.label_class',
                    'required' => false,
                ]],
                ['id', TextType::class, [
                    'label'    => 'form.label_id',
                    'required' => true,
                ]],
                ['limit', NumberType::class, [
                    'label'    => 'form.label_limit',
                    'required' => false,
                ]],
            ],
            'translation_domain' => 'Core23FacebookBundle',
        ]);
    }

    public function configureSettings(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'title'              => null,
            'translation_domain' => null,
            'icon'               => 'fa fa-facebook-official',
            'class'              => null,
            'id'                 => null,
            'limit'              => 10,
            'fields'             => 'type,message,description,permalink_url,picture,created_time',
            'template'           => '@Core23Facebook/Block/block_page_feed.html.twig',
        ]);

        $resolver->setRequired(['id']);
    }

    public function getBlockMetadata($code = null)
    {
        return new Metadata($this->getName(), $code ?? $this->getName(), null, 'Core23FacebookBundle', [
            'class' => 'fa fa-facebook-official',
        ]);
    }

    private function getData(array $settings): array
    {
        try {
            $accessToken = $this->getAccessToken();

            $response = $this->getFacebook()->get('/'.$settings['id'].'/feed?fields='.$settings['fields'], $accessToken);

            return $response->getGraphEdge()->asArray();
        } catch (FacebookSDKException $exception) {
            $this->logger->warning(sprintf('Facebook SDK Exception: %s', $exception->getMessage()));
        }

        return [];
    }
}
