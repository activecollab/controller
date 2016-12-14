<?php

/*
 * This file is part of the Active Collab Controller project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Controller\Test\Encoder;

use ActiveCollab\Controller\ActionResult\MovedResult\MovedResult;
use ActiveCollab\Controller\ActionResult\StatusResult\StatusResult;
use ActiveCollab\Controller\ActionResultEncoder\ActionResultEncoder;
use ActiveCollab\Controller\ActionResultEncoder\ValueEncoder\MovedResourceEncoder;
use ActiveCollab\Controller\Test\Base\TestCase;
use ActiveCollab\Controller\Test\Fixtures\ActionResultInContainer;
use Pimple\Container;
use Psr\Http\Message\ResponseInterface;

class MovedResourceEncoderTest extends TestCase
{
    private $container;

    /**
     * @var ActionResultInContainer
     */
    private $action_result_container;

    public function setUp()
    {
        parent::setUp();

        $this->container = new Container();
        $this->action_result_container = new ActionResultInContainer($this->container);
    }

    public function testShouldEncode()
    {
        $this->assertFalse((new MovedResourceEncoder())->shouldEncode([1, 2, 3]));
        $this->assertFalse((new MovedResourceEncoder())->shouldEncode(new StatusResult(200)));
        $this->assertTrue((new MovedResourceEncoder())->shouldEncode(new MovedResult('https://activecollab.com')));
    }

    public function testMovedTemporalyByDefault()
    {
        $this->assertFalse((new MovedResult('https://activecollab.com'))->isMovedPermanently());
    }

    public function testMovedTemporaly()
    {
        $response = $this->createResponse()->withHeader('X-Test', 'yes');

        $response = (new MovedResourceEncoder())->encode($response, new ActionResultEncoder($this->action_result_container), new MovedResult('https://activecollab.com'));
        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertContains('yes', $response->getHeaderLine('X-Test'));

        $this->assertSame('https://activecollab.com', $response->getHeaderLine('Location'));
        $this->assertSame(302, $response->getStatusCode());
        $this->assertSame('Found', $response->getReasonPhrase());
    }

    public function testMovedPermanently()
    {
        $response = $this->createResponse()->withHeader('X-Test', 'yes');

        $response = (new MovedResourceEncoder())->encode($response, new ActionResultEncoder($this->action_result_container), new MovedResult('https://activecollab.com', true));
        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertContains('yes', $response->getHeaderLine('X-Test'));

        $this->assertSame('https://activecollab.com', $response->getHeaderLine('Location'));
        $this->assertSame(301, $response->getStatusCode());
        $this->assertSame('Moved Permanently', $response->getReasonPhrase());
    }
}
