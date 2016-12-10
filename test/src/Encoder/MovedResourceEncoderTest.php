<?php

/*
 * This file is part of the Active Collab Controller project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Controller\Test\Encoder;

use ActiveCollab\Controller\ActionResult\MovedResource;
use ActiveCollab\Controller\ActionResult\StatusResult\Ok;
use ActiveCollab\Controller\ActionResultEncoder\ActionResultEncoder;
use ActiveCollab\Controller\ActionResultEncoder\ValueEncoder\MovedResourceEncoder;
use ActiveCollab\Controller\Test\Base\TestCase;
use Psr\Http\Message\ResponseInterface;

class MovedResourceEncoderTest extends TestCase
{
    public function testShouldEncode()
    {
        $this->assertFalse((new MovedResourceEncoder())->shouldEncode([1, 2, 3]));
        $this->assertFalse((new MovedResourceEncoder())->shouldEncode(new Ok()));
        $this->assertTrue((new MovedResourceEncoder())->shouldEncode(new MovedResource('https://activecollab.com')));
    }

    public function testMovedTemporalyByDefault()
    {
        $this->assertFalse((new MovedResource('https://activecollab.com'))->isMovedPermanently());
    }

    public function testMovedTemporaly()
    {
        $response = $this->createResponse()->withHeader('X-Test', 'yes');

        $response = (new MovedResourceEncoder())->encode($response, new ActionResultEncoder(), new MovedResource('https://activecollab.com'));
        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertContains('yes', $response->getHeaderLine('X-Test'));

        $this->assertSame('https://activecollab.com', $response->getHeaderLine('Location'));
        $this->assertSame(302, $response->getStatusCode());
        $this->assertSame('Found', $response->getReasonPhrase());
    }

    public function testMovedPermanently()
    {
        $response = $this->createResponse()->withHeader('X-Test', 'yes');

        $response = (new MovedResourceEncoder())->encode($response, new ActionResultEncoder(), new MovedResource('https://activecollab.com', true));
        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertContains('yes', $response->getHeaderLine('X-Test'));

        $this->assertSame('https://activecollab.com', $response->getHeaderLine('Location'));
        $this->assertSame(301, $response->getStatusCode());
        $this->assertSame('Moved Permanently', $response->getReasonPhrase());
    }
}
