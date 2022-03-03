<?php

/*
 * This file is part of the Active Collab Controller project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Controller\Test;

use ActiveCollab\Controller\ActionResult\Container\ActionResultContainerInterface;
use ActiveCollab\Controller\ActionResultEncoder\ActionResultEncoder;
use ActiveCollab\Controller\ActionResultEncoder\ValueEncoder\ArrayEncoder;
use ActiveCollab\Controller\Test\Base\TestCase;
use ActiveCollab\Controller\Test\Fixtures\ActionResultInContainer;
use Monolog\Handler\TestHandler;
use Monolog\Logger;
use Pimple\Container;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;
use RuntimeException;

class ActionResultEncoderTest extends TestCase
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

    public function testActionResultContainerIsAccessible()
    {
        $this->assertInstanceOf(ActionResultContainerInterface::class, (new ActionResultEncoder($this->action_result_container))->getActionResultContainer());
    }

    public function testLogWhenActionResultIsNotFoundInRequest()
    {
        $log_handler = new TestHandler();
        $logger = new Logger('Test', [$log_handler]);

        $action_result_encoder = new ActionResultEncoder($this->action_result_container);
        $action_result_encoder->setLogger($logger);
        $this->assertInstanceOf(LoggerInterface::class, $action_result_encoder->getLogger());

        call_user_func($action_result_encoder, $this->createRequest(), $this->createResponse());

        $this->assertCount(1, $log_handler->getRecords());

        $this->assertSame('Action result not found in value container.', $log_handler->getRecords()[0]['message']);
        $this->assertSame('ERROR', $log_handler->getRecords()[0]['level_name']);
    }

    public function testExceptionWhenNoMatchingEncoderIsFound()
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage("No matching encoder for value of array type found.");

        $this->action_result_container->setValue([1, 2, 3]);
        $this->assertTrue($this->action_result_container->hasValue());

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

        $this->action_result_container->setValue([1, 2, 3]);
        $this->assertTrue($this->action_result_container->hasValue());

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

        $this->action_result_container->setValue([1, 2, 3]);
        $this->assertTrue($this->action_result_container->hasValue());

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

        $this->action_result_container->setValue([1, 2, 3]);
        $this->assertTrue($this->action_result_container->hasValue());

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
