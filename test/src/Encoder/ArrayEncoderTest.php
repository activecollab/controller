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
use ActiveCollab\Controller\Test\Base\TestCase;
use ActiveCollab\Controller\Test\Fixtures\ActionResultInContainer;
use Pimple\Container;
use Psr\Http\Message\ResponseInterface;

class ArrayEncoderTest extends TestCase
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
        $this->assertFalse((new ArrayEncoder())->shouldEncode(null));
        $this->assertFalse((new ArrayEncoder())->shouldEncode(new StatusResult(200)));
        $this->assertTrue((new ArrayEncoder())->shouldEncode([1, 2, 3]));
    }

    public function testEncodeArray()
    {
        $response = $this->createResponse()->withHeader('X-Test', 'yes');

        $response = (new ArrayEncoder())->encode($response, new ActionResultEncoder($this->action_result_container), [1, 2, 3]);
        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertContains('yes', $response->getHeaderLine('X-Test'));
        $this->assertContains('application/json', $response->getHeaderLine('Content-Type'));

        $response_body = json_decode((string) $response->getBody(), true);

        $this->assertInternalType('array', $response_body);
        $this->assertSame([1, 2, 3], $response_body);
    }
}
