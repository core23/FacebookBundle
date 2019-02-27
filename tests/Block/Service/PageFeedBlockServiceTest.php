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
use Facebook\Facebook;
use Facebook\FacebookApp;
use Facebook\FacebookResponse;
use Facebook\GraphNodes\GraphEdge;
use Sonata\BlockBundle\Block\BlockContext;
use Sonata\BlockBundle\Model\Block;
use Sonata\BlockBundle\Model\BlockInterface;
use Sonata\BlockBundle\Test\AbstractBlockServiceTestCase;

final class PageFeedBlockServiceTest extends AbstractBlockServiceTestCase
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
        $app->expects($this->once())->method('getAccessToken')
            ->willReturn($token)
        ;

        $this->facebook->expects($this->once())->method('getApp')
            ->willReturn($app)
        ;

        $feedResponse = [
            ['foo' => 'bar'],
        ];

        $edge = $this->createMock(GraphEdge::class);
        $edge->expects($this->once())->method('asArray')
            ->willReturn($feedResponse)
        ;

        $response = $this->createMock(FacebookResponse::class);
        $response->expects($this->once())->method('getGraphEdge')
            ->willReturn($edge)
        ;

        $this->facebook->method('get')
            ->with($this->equalTo('/0815/feed?fields=type,message,description,permalink_url,picture,created_time'), $this->equalTo($token))
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

        $blockService = new PageFeedBlockService('block.service', $this->templating, $this->facebook);
        $blockService->execute($blockContext);

        $this->assertSame('@Core23Facebook/Block/block_page_feed.html.twig', $this->templating->view);

        $this->assertSame($blockContext, $this->templating->parameters['context']);
        $this->assertInternalType('array', $this->templating->parameters['settings']);
        $this->assertInstanceOf(BlockInterface::class, $this->templating->parameters['block']);

        $this->assertSame($feedResponse, $this->templating->parameters['feed']);
    }

    public function testDefaultSettings(): void
    {
        $blockService = new PageFeedBlockService('block.service', $this->templating, $this->facebook);
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
}
