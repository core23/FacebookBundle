<?php

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

class PageFeedBlockServiceTest extends AbstractBlockServiceTestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|Facebook
     */
    private $facebook;

    protected function setUp()
    {
        parent::setUp();

        $this->facebook = $this->createMock(Facebook::class);
    }

    public function testExecute()
    {
        $token = $this->createMock(AccessToken::class);

        $app = $this->createMock(FacebookApp::class);
        $app->expects($this->once())->method('getAccessToken')
            ->will($this->returnValue($token));

        $this->facebook->expects($this->once())->method('getApp')
            ->will($this->returnValue($app));

        $feedResponse = array(
            array('foo' => 'bar'),
        );

        $edge = $this->createMock(GraphEdge::class);
        $edge->expects($this->once())->method('asArray')
            ->will($this->returnValue($feedResponse));

        $response = $this->createMock(FacebookResponse::class);
        $response->expects($this->once())->method('getGraphEdge')
            ->will($this->returnValue($edge));

        $this->facebook->method('get')
            ->with($this->equalTo('/0815/feed?fields=type,message,description,permalink_url,picture,created_time'), $this->equalTo($token))
            ->will($this->returnValue($response));

        $block = new Block();

        $blockContext = new BlockContext($block, array(
            'title'    => 'Facebook Timeline',
            'template' => 'Core23FacebookBundle:Block:block_page_feed.html.twig',
            'id'       => '0815',
            'fields'   => 'type,message,description,permalink_url,picture,created_time',
        ));

        $blockService = new PageFeedBlockService('block.service', $this->templating, $this->facebook);
        $blockService->execute($blockContext);

        $this->assertSame('Core23FacebookBundle:Block:block_page_feed.html.twig', $this->templating->view);

        $this->assertSame($blockContext, $this->templating->parameters['context']);
        $this->assertInternalType('array', $this->templating->parameters['settings']);
        $this->assertInstanceOf(BlockInterface::class, $this->templating->parameters['block']);

        $this->assertSame($feedResponse, $this->templating->parameters['feed']);
    }

    public function testDefaultSettings()
    {
        $blockService = new PageFeedBlockService('block.service', $this->templating, $this->facebook);
        $blockContext = $this->getBlockContext($blockService);

        $this->assertSettings(array(
            'title'    => 'Facebook Timeline',
            'id'       => null,
            'limit'    => 10,
            'class'    => '',
            'fields'   => 'type,message,description,permalink_url,picture,created_time',
            'template' => 'Core23FacebookBundle:Block:block_page_feed.html.twig',
        ), $blockContext);
    }
}
