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
use ActiveCollab\Controller\Test\Base\TestCase;
use ActiveCollab\Controller\Test\Fixtures\ActionResultInContainer;
use Pimple\Container;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class ActionResultEncoderTest extends TestCase
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

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Action result not found in the container.
     */
    public function testExceptionWhenActionResultIsNotFoundInRequest()
    {
        call_user_func(new ActionResultEncoder($this->action_result_container), $this->createRequest(), $this->createResponse());
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage No matching encoder for value of array type found.
     */
    public function testExceptionWhenNoMatchingEncoderIsFound()
    {
        $this->action_result_container->set([1, 2, 3]);
        $this->assertTrue($this->action_result_container->has());

        $encoder = new ActionResultEncoder($this->action_result_container);
        $this->assertCount(0, $encoder->getValueEncoders());

        call_user_func($encoder, $this->createRequest(), $this->createResponse());
    }

    public function testAddValueEncoder()
    {
        $encoder = new ActionResultEncoder($this->action_result_container);
        $this->assertCount(0, $encoder->getValueEncoders());

        $encoder->addValueEncoder(new ArrayEncoder());
        $this->assertCount(1, $encoder->getValueEncoders());

        $this->action_result_container->set([1, 2, 3]);
        $this->assertTrue($this->action_result_container->has());

        /** @var ResponseInterface $response */
        $response = call_user_func($encoder, $this->createRequest(), $this->createResponse());
        $this->assertInstanceOf(ResponseInterface::class, $response);

        $response_body = json_decode((string) $response->getBody(), true);
        $this->assertSame([1, 2, 3], $response_body);
    }

    public function testEncoderCallsNextMiddleware()
    {
        $encoder = new ActionResultEncoder($this->action_result_container, new ArrayEncoder());
        $this->assertCount(1, $encoder->getValueEncoders());

        $this->action_result_container->set([1, 2, 3]);
        $this->assertTrue($this->action_result_container->has());

        /** @var ResponseInterface $response */
        $response = call_user_func($encoder, $this->createRequest(), $this->createResponse(), function (ServerRequestInterface $request, ResponseInterface $response) {
            return $response->withHeader('X-Next-Middleware', 'Header Found!');
        });
        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertSame('Header Found!', $response->getHeaderLine('X-Next-Middleware'));
    }

    public function testEncoderCanRunOnExit()
    {
        $encoder = (new ActionResultEncoder($this->action_result_container, new ArrayEncoder()))
            ->setEncodeOnExit();

        $this->assertTrue($encoder->getEncodeOnExit());

        $this->action_result_container->set([1, 2, 3]);
        $this->assertTrue($this->action_result_container->has());

        /** @var ResponseInterface $response */
        $response = call_user_func($encoder, $this->createRequest(), $this->createResponse(), function (ServerRequestInterface $request, ResponseInterface $response, callable $next = null) {
            $this->assertEmpty((string) $response->getBody());

            if ($next) {
                $response = $next($request, $response);
            }

            return $response;
        });
        $this->assertInstanceOf(ResponseInterface::class, $response);

        $response_body = json_decode((string) $response->getBody(), true);
        $this->assertSame([1, 2, 3], $response_body);
    }
}
