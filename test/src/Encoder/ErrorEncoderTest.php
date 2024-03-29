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
use ActiveCollab\Controller\ActionResultEncoder\ValueEncoder\ErrorEncoder;
use ActiveCollab\Controller\Test\Base\TestCase;
use ActiveCollab\Controller\Test\Fixtures\ActionResultInContainer;
use LogicException;
use ParseError;
use Pimple\Container;
use Psr\Http\Message\ResponseInterface;
use RuntimeException;

class ErrorEncoderTest extends TestCase
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
        $this->assertFalse((new ErrorEncoder())->shouldEncode([1, 2, 3]));
        $this->assertFalse((new ErrorEncoder())->shouldEncode(new StatusResult(200)));
        $this->assertTrue((new ErrorEncoder())->shouldEncode(new LogicException('Error in the app logic.')));
        $this->assertTrue((new ErrorEncoder())->shouldEncode(new ParseError('Error in the code.')));
    }

    public function testEncodeError()
    {
        $previous_exception = new LogicException('Error in the app logic.');
        $exception = new RuntimeException('Failing due to error in the app logic.', 0, $previous_exception);

        $response = $this->createResponse()->withHeader('X-Test', 'yes');

        $encoder = new ErrorEncoder();
        $this->assertFalse($encoder->getDisplayErrorDetails());

        $response = $encoder->encode($response, new ActionResultEncoder($this->action_result_container), $exception);
        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertStringContainsString('yes', $response->getHeaderLine('X-Test'));
        $this->assertStringContainsString('application/json', $response->getHeaderLine('Content-Type'));

        $response_body = json_decode((string) $response->getBody(), true);

        $this->assertIsArray($response_body);
        $this->assertSame('Failing due to error in the app logic.', $response_body['message']);
        $this->assertSame(RuntimeException::class, $response_body['type']);

        $this->assertIsArray($response_body['previous']);
        $this->assertSame('Error in the app logic.', $response_body['previous']['message']);
        $this->assertSame(LogicException::class, $response_body['previous']['type']);
    }

    public function testEncodeErrorWithDetails()
    {
        $previous_exception = new LogicException('Error in the app logic.');
        $exception = new RuntimeException('Failing due to error in the app logic.', 0, $previous_exception);

        $response = $this->createResponse()->withHeader('X-Test', 'yes');

        $encoder = new ErrorEncoder();
        $encoder->setDisplayErrorDetails(true);
        $this->assertTrue($encoder->getDisplayErrorDetails());

        $response = $encoder->encode($response, new ActionResultEncoder($this->action_result_container), $exception);
        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertStringContainsString('yes', $response->getHeaderLine('X-Test'));
        $this->assertStringContainsString('application/json', $response->getHeaderLine('Content-Type'));

        $response_body = json_decode((string) $response->getBody(), true);

        $this->assertIsArray($response_body);
        $this->assertSame(__FILE__, $response_body['file']);
        $this->assertNotEmpty($response_body['line']);
        $this->assertNotEmpty($response_body['trace']);

        $this->assertIsArray($response_body['previous']);
        $this->assertSame(__FILE__, $response_body['previous']['file']);
        $this->assertNotEmpty($response_body['previous']['line']);
        $this->assertNotEmpty($response_body['previous']['trace']);
    }
}
