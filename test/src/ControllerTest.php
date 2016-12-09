<?php

/*
 * This file is part of the Active Collab Controller project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Controller\Test;

use ActiveCollab\Controller\Test\Base\TestCase;
use ActiveCollab\Controller\Test\Fixtures\ErrorThrowingController;
use ActiveCollab\Controller\Test\Fixtures\FixedActionNameResolver;
use ActiveCollab\Controller\Test\Fixtures\TestController;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use RuntimeException;
use ParseError;
use LogicException;

/**
 * @package ActiveCollab\Controller\Test
 */
class ControllerTest extends TestCase
{
    public function testControllerName()
    {
        $test_controller = new TestController(new FixedActionNameResolver('throwPhpError'));
        $this->assertEquals('TestController', $test_controller->getControllerName());
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Action for GET / not found.
     */
    public function testActionNameNotResolved()
    {
        $test_controller = new TestController(new FixedActionNameResolver(new RuntimeException('Throw me!')));
        call_user_func($test_controller, $this->createRequest(), $this->createResponse());
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Action for GET / not found.
     */
    public function testActionNameIsEmpty()
    {
        $test_controller = new TestController(new FixedActionNameResolver(''));
        call_user_func($test_controller, $this->createRequest(), $this->createResponse());
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Action 'not an action name' not found
     */
    public function testActionNotFound()
    {
        $test_controller = new TestController(new FixedActionNameResolver('not an action name'));
        call_user_func($test_controller, $this->createRequest(), $this->createResponse());
    }

    public function testActionResult()
    {
        $test_controller = new TestController(new FixedActionNameResolver('index'));

        /** @var ServerRequestInterface $modified_request */
        $modified_request = null;

        $response = call_user_func($test_controller, $this->createRequest(), $this->createResponse(), function (ServerRequestInterface $request, ResponseInterface $response) use (&$modified_request) {
            $modified_request = $request;

            return $response;
        });
        $this->assertInstanceOf(ResponseInterface::class, $response);

        /** @var RuntimeException $action_result */
        $action_result = $modified_request->getAttribute($test_controller->getActionResultAttributeName());
        $this->assertInternalType('array', $action_result);
        $this->assertSame([1, 2, 3], $action_result);
    }

    public function testActionResultAttributeNameChange()
    {
        $test_controller = new TestController(new FixedActionNameResolver('index'), 'new_action_result_key');

        /** @var ServerRequestInterface $modified_request */
        $modified_request = null;

        $response = call_user_func($test_controller, $this->createRequest(), $this->createResponse(), function (ServerRequestInterface $request, ResponseInterface $response) use (&$modified_request) {
            $modified_request = $request;

            return $response;
        });
        $this->assertInstanceOf(ResponseInterface::class, $response);

        /** @var RuntimeException $action_result */
        $this->assertArrayNotHasKey('action_result', $modified_request->getAttributes());
        $this->assertArrayHasKey('new_action_result_key', $modified_request->getAttributes());

        $action_result = $modified_request->getAttribute('new_action_result_key');
        $this->assertInternalType('array', $action_result);
        $this->assertSame([1, 2, 3], $action_result);
    }

    public function testErrorsAreClientSafe()
    {
        $controller = new ErrorThrowingController(new FixedActionNameResolver('throwPhpError'));

        /** @var ServerRequestInterface $modified_request */
        $modified_request = null;

        $response = call_user_func($controller, $this->createRequest(), $this->createResponse(), function (ServerRequestInterface $request, ResponseInterface $response) use (&$modified_request) {
            $modified_request = $request;

            return $response;
        });
        $this->assertInstanceOf(ResponseInterface::class, $response);

        /** @var RuntimeException $action_result */
        $action_result = $modified_request->getAttribute($controller->getActionResultAttributeName());
        $this->assertInstanceOf(RuntimeException::class, $action_result);
        $this->assertInstanceOf(ParseError::class, $action_result->getPrevious());
    }

    public function testExceptionsAreClientSafe()
    {
        $controller = new ErrorThrowingController(new FixedActionNameResolver('throwException'));

        /** @var ServerRequestInterface $modified_request */
        $modified_request = null;

        $response = call_user_func($controller, $this->createRequest(), $this->createResponse(), function (ServerRequestInterface $request, ResponseInterface $response) use (&$modified_request) {
            $modified_request = $request;

            return $response;
        });
        $this->assertInstanceOf(ResponseInterface::class, $response);

        /** @var RuntimeException $action_result */
        $action_result = $modified_request->getAttribute($controller->getActionResultAttributeName());
        $this->assertInstanceOf(RuntimeException::class, $action_result);
        $this->assertInstanceOf(LogicException::class, $action_result->getPrevious());
    }
}
