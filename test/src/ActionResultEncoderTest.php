<?php

/*
 * This file is part of the Active Collab Controller project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Controller\Test;

use ActiveCollab\Controller\ActionResultEncoder\ActionResultEncoder;
use ActiveCollab\Controller\ActionResultEncoder\ValueEncoder\ArrayEncoder;
use Psr\Http\Message\ResponseInterface;
use ActiveCollab\Controller\Test\Base\TestCase;
use Psr\Http\Message\ServerRequestInterface;

class ActionResultEncoderTest extends TestCase
{
    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Request attribute name is required.
     */
    public function testRequestAttributeNameCantBeEmpty()
    {
        new ActionResultEncoder('');
    }

    public function testDefaultActionResultAttribute()
    {
        $this->assertSame('action_result', (new ActionResultEncoder())->getRequestAttributeName());
    }

    public function testDefaultActionResultAttributeNameCantBeEmpty()
    {
        (new ActionResultEncoder())->setRequestAttributeName('');
    }

    public function testActionResultAttributeCanBeChanged()
    {
        $this->assertSame('change_attribute_name', (new ActionResultEncoder())->setRequestAttributeName('change_attribute_name')->getRequestAttributeName());
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Request attribute 'action_result' not found.
     */
    public function testExceptionWhenActionResultIsNotFoundInRequest()
    {
        call_user_func(new ActionResultEncoder(), $this->createRequest(), $this->createResponse());
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage No matching encoder for value of array type found.
     */
    public function testExceptionWhenNoMatchingEncoderIsFound()
    {
        $encoder = new ActionResultEncoder();
        $this->assertCount(0, $encoder->getValueEncoders());

        call_user_func($encoder, $this->createRequest()->withAttribute('action_result', [1, 2, 3]), $this->createResponse());
    }

    public function testAddValueEncoder()
    {
        $encoder = new ActionResultEncoder();
        $this->assertCount(0, $encoder->getValueEncoders());

        $encoder->addValueEncoder(new ArrayEncoder());
        $this->assertCount(1, $encoder->getValueEncoders());

        /** @var ResponseInterface $response */
        $response = call_user_func($encoder, $this->createRequest()->withAttribute('action_result', [1, 2, 3]), $this->createResponse());
        $this->assertInstanceOf(ResponseInterface::class, $response);

        $response_body = json_decode((string) $response->getBody(), true);
        $this->assertSame([1, 2, 3], $response_body);
    }

    public function testEncoderCallsNextMiddleware()
    {
        $encoder = new ActionResultEncoder('action_result', new ArrayEncoder());
        $this->assertCount(1, $encoder->getValueEncoders());

        /** @var ResponseInterface $response */
        $response = call_user_func($encoder, $this->createRequest()->withAttribute('action_result', [1, 2, 3]), $this->createResponse(), function(ServerRequestInterface $request, ResponseInterface $response) {
            return $response->withHeader('X-Next-Middleware', 'Header Found!');
        });
        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertSame('Header Found!', $response->getHeaderLine('X-Next-Middleware'));
    }
}
