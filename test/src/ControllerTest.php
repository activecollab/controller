<?php

/*
 * This file is part of the Active Collab Controller project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Controller\Test;

use ActiveCollab\Controller\ActionResult\MovedResult\MovedResultInterface;
use ActiveCollab\Controller\ActionResult\StatusResult\StatusResultInterface;
use ActiveCollab\Controller\ControllerInterface;
use ActiveCollab\Controller\Test\Base\TestCase;
use ActiveCollab\Controller\Test\Fixtures\ActionResultInContainer;
use ActiveCollab\Controller\Test\Fixtures\ErrorThrowingController;
use ActiveCollab\Controller\Test\Fixtures\FixedActionNameResolver;
use ActiveCollab\Controller\Test\Fixtures\TestController;
use InvalidArgumentException;
use LogicException;
use Monolog\Handler\TestHandler;
use Monolog\Logger;
use ParseError;
use Pimple\Container;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;
use RuntimeException;

/**
 * @package ActiveCollab\Controller\Test
 */
class ControllerTest extends TestCase
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

    public function testControllerIsConfigured()
    {
        $default_properties = (new \ReflectionClass(TestController::class))->getDefaultProperties();
        $this->assertArrayHasKey('is_configured', $default_properties);

        $this->assertFalse($default_properties['is_configured']);

        $controller = new TestController(new FixedActionNameResolver('throwPhpError'), $this->action_result_container);
        $this->assertTrue($controller->is_configured);
    }

    public function testControllerName()
    {
        $test_controller = new TestController(new FixedActionNameResolver('throwPhpError'), $this->action_result_container);
        $this->assertEquals('TestController', $test_controller->getControllerName());

        require __DIR__ . '/Fixtures/GlobalNamespaceController.php';

        $test_controller = new \GlobalNamespaceController(new FixedActionNameResolver('throwPhpError'), $this->action_result_container);
        $this->assertEquals('GlobalNamespaceController', $test_controller->getControllerName());
    }

    public function testActionNameNotResolved()
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage("Action for GET / not found.");

        $test_controller = new TestController(new FixedActionNameResolver(new RuntimeException('Throw me!')), $this->action_result_container);
        call_user_func($test_controller, $this->createRequest(), $this->createResponse());
    }

    public function testActionNameIsEmpty()
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage("Action for GET / not found.");

        $test_controller = new TestController(new FixedActionNameResolver(''), $this->action_result_container);
        call_user_func($test_controller, $this->createRequest(), $this->createResponse());
    }

    public function testActionNotFound()
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage("Action 'not an action name' not found");

        $test_controller = new TestController(new FixedActionNameResolver('not an action name'), $this->action_result_container);
        call_user_func($test_controller, $this->createRequest(), $this->createResponse());
    }

    public function testBeforeInterceptsAction()
    {
        $before_should_return = [3, 2, 1];

        $test_controller = new TestController(new FixedActionNameResolver('index'), $this->action_result_container);
        $this->assertSame($before_should_return, $test_controller->setBeforeShouldReturn($before_should_return)->getBeforeShouldReturn());

        $response = call_user_func($test_controller, $this->createRequest(), $this->createResponse());
        $this->assertInstanceOf(ResponseInterface::class, $response);

        /** @var RuntimeException $action_result */
        $action_result = $this->action_result_container->getValue();
        $this->assertIsArray($action_result);
        $this->assertSame($before_should_return, $action_result);
    }

    public function testActionResult()
    {
        $test_controller = new TestController(new FixedActionNameResolver('index'), $this->action_result_container);

        $response = call_user_func($test_controller, $this->createRequest(), $this->createResponse());
        $this->assertInstanceOf(ResponseInterface::class, $response);

        /** @var RuntimeException $action_result */
        $action_result = $this->action_result_container->getValue();
        $this->assertIsArray($action_result);
        $this->assertSame([1, 2, 3], $action_result);
    }

    public function testNextMiddlewareIsCalled()
    {
        $test_controller = new TestController(new FixedActionNameResolver('index'), $this->action_result_container);

        /** @var ResponseInterface $response */
        $response = call_user_func($test_controller, $this->createRequest(), $this->createResponse(), function(ServerRequestInterface $request, ResponseInterface $response, callable $next = null) {
            $response = $response->withHeader('X-NextIsCalled', 'yes!');

            if ($next) {
                $response = $next($request, $response);
            }

            return $response;
        });
        $this->assertInstanceOf(ResponseInterface::class, $response);

        $this->assertSame('yes!', $response->getHeaderLine('X-NextIsCalled'));
    }

    public function testErrorsAreClientSafe()
    {
        $controller = new ErrorThrowingController(new FixedActionNameResolver('throwPhpError'), $this->action_result_container);

        $response = call_user_func($controller, $this->createRequest(), $this->createResponse());
        $this->assertInstanceOf(ResponseInterface::class, $response);

        /** @var RuntimeException $action_result */
        $action_result = $this->action_result_container->getValue();

        $this->assertInstanceOf(RuntimeException::class, $action_result);
        $this->assertInstanceOf(ParseError::class, $action_result->getPrevious());
    }

    public function testClientSafePhpErrorMessageCantBeEmpty()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Client safe exception message can't be empty.");

        (new ErrorThrowingController(new FixedActionNameResolver('throwPhpError'), $this->action_result_container))
            ->setClientSafeExceptionMessage('');
    }

    public function testClientSafePhpErrorMessageCanBeChanged()
    {
        $controller = (new ErrorThrowingController(new FixedActionNameResolver('throwPhpError'), $this->action_result_container))
            ->setClientSafeExceptionMessage('Sorry :( {message}');

        $this->assertSame('Sorry :( {message}', $controller->getClientSafeExceptionMessage());

        $response = call_user_func($controller, $this->createRequest(), $this->createResponse());
        $this->assertInstanceOf(ResponseInterface::class, $response);

        /** @var RuntimeException $action_result */
        $action_result = $this->action_result_container->getValue();
        $this->assertInstanceOf(RuntimeException::class, $action_result);
        $this->assertEquals('Sorry :( Error parsing our awesome code.', $action_result->getMessage());
    }

    public function testPhpErrorLogMessageCantBeEmpty()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Log PHP error message can't be empty.");

        (new ErrorThrowingController(new FixedActionNameResolver('throwPhpError'), $this->action_result_container))
            ->setLogPhpErrorMessage('');
    }

    public function testPhpErrorLogMessageSet()
    {
        $controller = (new ErrorThrowingController(new FixedActionNameResolver('throwPhpError'), $this->action_result_container))
            ->setLogPhpErrorMessage('Failed due to a PHP error');

        $this->assertSame('Failed due to a PHP error', $controller->getLogPhpErrorMessage());
    }

    public function testPhpErrorsAreLogged()
    {
        $test_handler = new TestHandler();
        $logger = new Logger('test', [$test_handler]);

        $controller = (new ErrorThrowingController(new FixedActionNameResolver('throwPhpError'), $this->action_result_container, $logger))
            ->setLogPhpErrorMessage('Failed due to a PHP error');

        $this->assertInstanceOf(LoggerInterface::class, $controller->getLogger());

        $response = call_user_func($controller, $this->createRequest(), $this->createResponse());
        $this->assertInstanceOf(ResponseInterface::class, $response);

        $this->assertCount(1, $test_handler->getRecords());

        $this->assertSame('Failed due to a PHP error', $test_handler->getRecords()[0]['message']);
        $this->assertInstanceOf(ParseError::class, $test_handler->getRecords()[0]['context']['exception']);
    }

    public function testExceptionsAreClientSafe()
    {
        $controller = new ErrorThrowingController(new FixedActionNameResolver('throwException'), $this->action_result_container);

        /** @var ServerRequestInterface $modified_request */
        $modified_request = null;

        $response = call_user_func($controller, $this->createRequest(), $this->createResponse());
        $this->assertInstanceOf(ResponseInterface::class, $response);

        /** @var RuntimeException $action_result */
        $action_result = $this->action_result_container->getValue();
        $this->assertInstanceOf(RuntimeException::class, $action_result);
        $this->assertInstanceOf(LogicException::class, $action_result->getPrevious());
    }

    public function testClientSafeExceptionMessageCanBeChanged()
    {
        $controller = (new ErrorThrowingController(new FixedActionNameResolver('throwException'), $this->action_result_container))
            ->setClientSafeExceptionMessage('Sorry :( {message}');

        $this->assertSame('Sorry :( {message}', $controller->getClientSafeExceptionMessage());

        $response = call_user_func($controller, $this->createRequest(), $this->createResponse());
        $this->assertInstanceOf(ResponseInterface::class, $response);

        /** @var RuntimeException $action_result */
        $action_result = $this->action_result_container->getValue();
        $this->assertInstanceOf(RuntimeException::class, $action_result);
        $this->assertEquals('Sorry :( App logic is broken.', $action_result->getMessage());
    }

    public function testExceptionLogMessageCantBeEmpty()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Log exception message can't be empty.");

        (new ErrorThrowingController(new FixedActionNameResolver('throwPhpError'), $this->action_result_container))
            ->setLogExceptionMessage('');
    }

    public function testExceptionLogMessageSet()
    {
        $controller = (new ErrorThrowingController(new FixedActionNameResolver('throwException'), $this->action_result_container))
            ->setLogExceptionMessage('Failed due to an exception');

        $this->assertSame('Failed due to an exception', $controller->getLogExceptionMessage());
    }

    public function testExceptionsAreLogged()
    {
        $test_handler = new TestHandler();
        $logger = new Logger('test', [$test_handler]);

        $controller = (new ErrorThrowingController(new FixedActionNameResolver('throwException'), $this->action_result_container, $logger))
            ->setLogExceptionMessage('Failed due to an exception');

        $this->assertSame('Failed due to an exception', $controller->getLogExceptionMessage());
        $this->assertInstanceOf(LoggerInterface::class, $controller->getLogger());

        /** @var ServerRequestInterface $modified_request */
        $modified_request = null;

        $response = call_user_func($controller, $this->createRequest(), $this->createResponse());
        $this->assertInstanceOf(ResponseInterface::class, $response);

        $this->assertCount(1, $test_handler->getRecords());
        $this->assertSame('Failed due to an exception', $test_handler->getRecords()[0]['message']);
        $this->assertInstanceOf(LogicException::class, $test_handler->getRecords()[0]['context']['exception']);
    }

    public function testOk()
    {
        $result = $this->createTestController()->ok();

        $this->assertInstanceOf(StatusResultInterface::class, $result);
        $this->assertSame(200, $result->getStatusCode());
    }

    public function testCreated()
    {
        $payload = [1, 2, 3];

        $result = $this->createTestController()->created($payload);

        $this->assertInstanceOf(StatusResultInterface::class, $result);
        $this->assertSame(201, $result->getStatusCode());
        $this->assertSame($payload, $result->getPayload());
    }

    public function testBadRequest()
    {
        $result = $this->createTestController()->badRequest();

        $this->assertInstanceOf(StatusResultInterface::class, $result);
        $this->assertSame(400, $result->getStatusCode());
    }

    public function testForbidden()
    {
        $result = $this->createTestController()->forbidden();

        $this->assertInstanceOf(StatusResultInterface::class, $result);
        $this->assertSame(403, $result->getStatusCode());
    }

    public function testNotFound()
    {
        $result = $this->createTestController()->notFound();

        $this->assertInstanceOf(StatusResultInterface::class, $result);
        $this->assertSame(404, $result->getStatusCode());
    }

    public function testConflict()
    {
        $result = $this->createTestController()->conflict();

        $this->assertInstanceOf(StatusResultInterface::class, $result);
        $this->assertSame(409, $result->getStatusCode());
    }

    /**
     * @param bool $is_moved_permanently
     * @dataProvider getIsMovedPermanentlyValues
     */
    public function testMoved(bool $is_moved_permanently)
    {
        $result = $this->createTestController()->moved('https://activecollab.com', $is_moved_permanently);

        $this->assertInstanceOf(MovedResultInterface::class, $result);
        $this->assertSame('https://activecollab.com', $result->getUrl());
        $this->assertSame($is_moved_permanently, $result->isMovedPermanently());
    }

    public function getIsMovedPermanentlyValues()
    {
        return [[false], [true]];
    }

    /**
     * @return TestController|ControllerInterface
     */
    private function createTestController()
    {
        $test_handler = new TestHandler();
        $logger = new Logger('test', [$test_handler]);

        return new TestController(new FixedActionNameResolver('not an action name'), $this->action_result_container, $logger);
    }
}
