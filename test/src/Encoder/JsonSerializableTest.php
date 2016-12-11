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
use ActiveCollab\Controller\ActionResultEncoder\ValueEncoder\JsonSerializableEncoder;
use ActiveCollab\Controller\Test\Base\TestCase;
use ActiveCollab\Controller\Test\Fixtures\ArrayAsJson;
use Psr\Http\Message\ResponseInterface;

class JsonSerializableTest extends TestCase
{
    public function testShouldEncode()
    {
        $this->assertFalse((new JsonSerializableEncoder())->shouldEncode(null));
        $this->assertFalse((new JsonSerializableEncoder())->shouldEncode(new StatusResult(200)));
        $this->assertFalse((new JsonSerializableEncoder())->shouldEncode([3, 2, 1]));
        $this->assertTrue((new JsonSerializableEncoder())->shouldEncode(new ArrayAsJson([3, 2, 1])));
    }

    public function testEncodeArray()
    {
        $response = $this->createResponse()->withHeader('X-Test', 'yes');

        $to_encode = new ArrayAsJson([3, 2, 1]);
        $this->assertInstanceOf(\JsonSerializable::class, $to_encode);

        $response = (new JsonSerializableEncoder())->encode($response, new ActionResultEncoder(), $to_encode);
        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertContains('yes', $response->getHeaderLine('X-Test'));
        $this->assertContains('application/json', $response->getHeaderLine('Content-Type'));

        $response_body = json_decode((string) $response->getBody(), true);

        $this->assertInternalType('array', $response_body);
        $this->assertSame([3, 2, 1], $response_body);
    }
}
