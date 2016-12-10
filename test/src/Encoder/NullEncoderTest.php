<?php

/*
 * This file is part of the Active Collab Controller project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Controller\Test\Encoder;

use ActiveCollab\Controller\ActionResult\StatusResult\Ok;
use ActiveCollab\Controller\ActionResultEncoder\ActionResultEncoder;
use ActiveCollab\Controller\ActionResultEncoder\ValueEncoder\NullEncoder;
use ActiveCollab\Controller\Test\Base\TestCase;
use Psr\Http\Message\ResponseInterface;

class NullEncoderTest extends TestCase
{
    public function testShouldEncode()
    {
        $this->assertFalse((new NullEncoder())->shouldEncode([1, 2, 3]));
        $this->assertFalse((new NullEncoder())->shouldEncode(new Ok()));
        $this->assertTrue((new NullEncoder())->shouldEncode(null));
    }

    public function testEncodeNull()
    {
        $response = $this->createResponse()->withHeader('X-Test', 'yes');

        $response = (new NullEncoder())->encode($response, new ActionResultEncoder(), null);
        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertContains('yes', $response->getHeaderLine('X-Test'));
        $this->assertContains('application/json', $response->getHeaderLine('Content-Type'));

        $response_body = json_decode((string) $response->getBody(), true);

        $this->assertEmpty($response_body);
    }
}
