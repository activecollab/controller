<?php

/*
 * This file is part of the Active Collab Controller project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Controller\Test\Encoder;

use ActiveCollab\Controller\ActionResult\StatusResult\StatusResult;
use ActiveCollab\Controller\ActionResultEncoder\ActionResultEncoder;
use ActiveCollab\Controller\ActionResultEncoder\ValueEncoder\ArrayEncoder;
use ActiveCollab\Controller\ActionResultEncoder\ValueEncoder\StatusEncoder;
use ActiveCollab\Controller\Test\Base\TestCase;
use ActiveCollab\Controller\Test\Fixtures\ActionResultInContainer;
use Pimple\Container;
use Psr\Http\Message\ResponseInterface;

class StatusEncoderTest extends TestCase
{
    private $container;

    /**
     * @var ActionResultInContainer
     */
    private $action_result_container;

    public function setUp(): void
    {
        parent::setUp();

        $this->container = new Container();
        $this->action_result_container = new ActionResultInContainer($this->container);
    }

    public function testShouldEncode()
    {
        $this->assertFalse((new StatusEncoder())->shouldEncode([1, 2, 3]));
        $this->assertFalse((new StatusEncoder())->shouldEncode(null));
        $this->assertTrue((new StatusEncoder())->shouldEncode(new StatusResult(200, 'Ok')));
    }

    public function testEncodeStatus()
    {
        $response = $this->createResponse()->withHeader('X-Test', 'yes');

        $response = (new StatusEncoder())->encode($response, new ActionResultEncoder($this->action_result_container), new StatusResult(200, 'All good.'));
        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertContains('yes', $response->getHeaderLine('X-Test'));

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('All good.', $response->getReasonPhrase());
    }

    public function testEncodeStatusWithPayload()
    {
        $data_to_encode = [1, 2, 3];

        $response = $this->createResponse()->withHeader('X-Test', 'yes');

        $encoder = new ActionResultEncoder($this->action_result_container, new ArrayEncoder());
        $this->assertCount(1, $encoder->getValueEncoders());

        $response = (new StatusEncoder())->encode($response, $encoder, new StatusResult(200, 'All good.', $data_to_encode));
        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertContains('yes', $response->getHeaderLine('X-Test'));
        $this->assertContains('application/json', $response->getHeaderLine('Content-Type'));

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('All good.', $response->getReasonPhrase());

        $response_body = json_decode((string) $response->getBody(), true);

        $this->assertInternalType('array', $response_body);
        $this->assertSame($data_to_encode, $response_body);
    }
}
