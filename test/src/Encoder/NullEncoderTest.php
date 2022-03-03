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
use ActiveCollab\Controller\ActionResultEncoder\ValueEncoder\NullEncoder;
use ActiveCollab\Controller\Test\Base\TestCase;
use ActiveCollab\Controller\Test\Fixtures\ActionResultInContainer;
use Pimple\Container;
use Psr\Http\Message\ResponseInterface;

class NullEncoderTest extends TestCase
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
        $this->assertFalse((new NullEncoder())->shouldEncode([1, 2, 3]));
        $this->assertFalse((new NullEncoder())->shouldEncode(new StatusResult(200)));
        $this->assertTrue((new NullEncoder())->shouldEncode(null));
    }

    public function testEncodeNull()
    {
        $response = $this->createResponse()->withHeader('X-Test', 'yes');

        $response = (new NullEncoder())->encode($response, new ActionResultEncoder($this->action_result_container), null);
        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertStringContainsString('yes', $response->getHeaderLine('X-Test'));
        $this->assertStringContainsString('application/json', $response->getHeaderLine('Content-Type'));

        $response_body = json_decode((string) $response->getBody(), true);

        $this->assertEmpty($response_body);
    }
}
