<?php

declare(strict_types=1);

/*
 * (c) Christian Gripp <mail@core23.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Core23\FacebookBundle\Tests\Block\Service;

use Core23\FacebookBundle\Block\Service\PageFeedBlockService;
use Facebook\Authentication\AccessToken;
use Facebook\Exceptions\FacebookSDKException;
use Facebook\Facebook;
use Facebook\FacebookApp;
use Facebook\FacebookResponse;
use Facebook\GraphNodes\GraphEdge;
use Sonata\BlockBundle\Block\BlockContext;
use Sonata\BlockBundle\Form\Mapper\FormMapper;
use Sonata\BlockBundle\Model\Block;
use Sonata\BlockBundle\Test\BlockServiceTestCase;
use Symfony\Component\HttpFoundation\Response;

final class PageFeedBlockServiceTest extends BlockServiceTestCase
{
    private $facebook;

    protected function setUp(): void
    {
        parent::setUp();

        $this->facebook = $this->createMock(Facebook::class);
    }

    public function testExecute(): void
    {
        $token = $this->createMock(AccessToken::class);

        $app = $this->createMock(FacebookApp::class);
        $app->expects(static::once())->method('getAccessToken')
            ->willReturn($token)
        ;

        $this->facebook->expects(static::once())->method('getApp')
            ->willReturn($app)
        ;

        $feedResponse = [
            ['foo' => 'bar'],
        ];

        $edge = $this->createMock(GraphEdge::class);
        $edge->expects(static::once())->method('asArray')
            ->willReturn($feedResponse)
        ;

        $response = $this->createMock(FacebookResponse::class);
        $response->expects(static::once())->method('getGraphEdge')
            ->willReturn($edge)
        ;

        $this->facebook->method('get')
            ->with(static::equalTo('/0815/feed?fields=type,message,description,permalink_url,picture,created_time'), static::equalTo($token))
            ->willReturn($response)
        ;

        $block = new Block();

        $blockContext = new BlockContext($block, [
            'title'              => null,
            'translation_domain' => null,
            'template'           => '@Core23Facebook/Block/block_page_feed.html.twig',
            'id'                 => '0815',
            'fields'             => 'type,message,description,permalink_url,picture,created_time',
        ]);

        $response = new Response();

        $this->twig->expects(static::once())->method('render')
            ->with(
                '@Core23Facebook/Block/block_page_feed.html.twig',
                [
                    'context'    => $blockContext,
                    'settings'   => $blockContext->getSettings(),
                    'block'      => $blockContext->getBlock(),
                    'feed'       => $feedResponse,
                ]
            )
            ->willReturn('TWIG_CONTENT')
        ;

        $blockService = new PageFeedBlockService($this->twig, $this->facebook);

        static::assertSame($response, $blockService->execute($blockContext, $response));
        static::assertSame('TWIG_CONTENT', $response->getContent());
    }

    public function testExecuteThrowsFacebookException(): void
    {
        $token = $this->createMock(AccessToken::class);

        $app = $this->createMock(FacebookApp::class);
        $app->expects(static::once())->method('getAccessToken')
            ->willReturn($token)
        ;

        $this->facebook->expects(static::once())->method('getApp')
            ->willReturn($app)
        ;

        $this->facebook->method('get')
            ->with(static::equalTo('/0815/feed?fields=type,message,description,permalink_url,picture,created_time'), static::equalTo($token))
            ->willThrowException(new FacebookSDKException())
        ;

        $block = new Block();

        $blockContext = new BlockContext($block, [
            'title'              => null,
            'translation_domain' => null,
            'template'           => '@Core23Facebook/Block/block_page_feed.html.twig',
            'id'                 => '0815',
            'fields'             => 'type,message,description,permalink_url,picture,created_time',
        ]);

        $response = new Response();

        $this->twig->expects(static::once())->method('render')
            ->with(
                '@Core23Facebook/Block/block_page_feed.html.twig',
                [
                    'context'    => $blockContext,
                    'settings'   => $blockContext->getSettings(),
                    'block'      => $blockContext->getBlock(),
                    'feed'       => [],
                ]
            )
            ->willReturn('TWIG_CONTENT')
        ;

        $blockService = new PageFeedBlockService($this->twig, $this->facebook);

        static::assertSame($response, $blockService->execute($blockContext, $response));
        static::assertSame('TWIG_CONTENT', $response->getContent());
    }

    public function testDefaultSettings(): void
    {
        $blockService = new PageFeedBlockService($this->twig, $this->facebook);
        $blockContext = $this->getBlockContext($blockService);

        $this->assertSettings([
            'title'              => null,
            'translation_domain' => null,
            'icon'               => 'fa fa-facebook-official',
            'class'              => null,
            'id'                 => null,
            'limit'              => 10,
            'fields'             => 'type,message,description,permalink_url,picture,created_time',
            'template'           => '@Core23Facebook/Block/block_page_feed.html.twig',
        ], $blockContext);
    }

    public function testGetMetadata(): void
    {
        $blockService = new PageFeedBlockService($this->twig, $this->facebook);

        $metadata = $blockService->getMetadata();

        static::assertSame('core23_facebook.block.page_feed', $metadata->getTitle());
        static::assertNull($metadata->getImage());
        static::assertSame('Core23FacebookBundle', $metadata->getDomain());
        static::assertSame([
            'class' => 'fa fa-facebook-official',
        ], $metadata->getOptions());
    }

    public function testConfigureEditForm(): void
    {
        $blockService = new PageFeedBlockService($this->twig, $this->facebook);

        $block = new Block();

        $formMapper = $this->createMock(FormMapper::class);
        $formMapper->expects(static::once())->method('add');

        $blockService->configureEditForm($formMapper, $block);
    }
}
