<?php

/*
 * This file is part of the Active Collab Controller project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Controller\Test;

use ActiveCollab\Controller\Test\Base\TestCase;
use ActiveCollab\Controller\Test\Fixtures\ActionResultInContainer;
use ActiveCollab\Controller\Test\Fixtures\FixedActionNameResolver;
use ActiveCollab\Controller\Test\Fixtures\TestController;
use Pimple\Container;
use stdClass;

class RequestParamGetterTest extends TestCase
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

    public function testGetQueryParam()
    {
        $request = $this->createRequest(
            'GET',
            '/',
            [
                'search' => 'for this',
                'extended' => true,
            ]
        );

        $controller = new TestController(
            new FixedActionNameResolver('throwPhpError'),
            $this->action_result_container
        );

        $this->assertSame('for this', $controller->getQueryParam($request, 'search'));
        $this->assertSame(true, $controller->getQueryParam($request, 'extended'));
    }

    public function testQueryParamReturnsDefaultWhenParamNotFound()
    {
        $request = $this->createRequest(
            'GET',
            '/',
            [
                'search' => 'for this',
                'extended' => true,
            ]
        );

        $controller = new TestController(
            new FixedActionNameResolver('throwPhpError'),
            $this->action_result_container
        );

        $this->assertNull($controller->getQueryParam($request, 'unknown'));
        $this->assertSame(123, $controller->getQueryParam($request, 'unknown', 123));
    }

    public function testGetParsedBodyParamFromArray()
    {
        $request = $this->createRequest(
            'GET',
            '/',
            [],
            [
                'search' => 'for this',
                'extended' => true,
            ]
        );

        $controller = new TestController(new FixedActionNameResolver('throwPhpError'), $this->action_result_container);

        $this->assertSame('for this', $controller->getParsedBodyParam($request, 'search'));
        $this->assertSame(true, $controller->getParsedBodyParam($request, 'extended'));
    }

    public function testGetParedBodyParamReturnsDefaultWhenParamNotFound()
    {
        $request = $this->createRequest();

        $controller = new TestController(new FixedActionNameResolver('throwPhpError'), $this->action_result_container);

        $this->assertNull($controller->getParsedBodyParam($request, 'unknown'));
        $this->assertSame(123, $controller->getParsedBodyParam($request, 'unknown', 123));
    }

    public function testGetParsedBodyParamFromObject()
    {
        $object = new stdClass();
        $object->property1 = 'something';
        $object->property2 = 123;

        $request = $this->createRequest()->withParsedBody($object);

        $controller = new TestController(new FixedActionNameResolver('throwPhpError'), $this->action_result_container);

        $this->assertSame('something', $controller->getParsedBodyParam($request, 'property1'));
        $this->assertSame(123, $controller->getParsedBodyParam($request, 'property2'));
    }

    public function testGetCookieParamFromArray()
    {
        $request = $this->createRequest()->withCookieParams([
            'search' => 'for this',
            'extended' => true,
        ]);

        $controller = new TestController(new FixedActionNameResolver('throwPhpError'), $this->action_result_container);

        $this->assertSame('for this', $controller->getCookieParam($request, 'search'));
        $this->assertSame(true, $controller->getCookieParam($request, 'extended'));
    }

    public function testGetCookieParamReturnsDefaultWhenParamNotFound()
    {
        $request = $this->createRequest();

        $controller = new TestController(new FixedActionNameResolver('throwPhpError'), $this->action_result_container);

        $this->assertNull($controller->getCookieParam($request, 'unknown'));
        $this->assertSame(123, $controller->getCookieParam($request, 'unknown', 123));
    }

    public function testGetServerParamFromArray()
    {
        $request = $this->createRequest();

        $controller = new TestController(
            new FixedActionNameResolver('throwPhpError'),
            $this->action_result_container
        );

        $this->assertSame('GET', $controller->getServerParam($request, 'REQUEST_METHOD'));
        $this->assertSame('/', $controller->getServerParam($request, 'REQUEST_URI'));
    }

    public function testGetServerParamReturnsDefaultWhenParamNotFound()
    {
        $request = $this->createRequest();

        $controller = new TestController(new FixedActionNameResolver('throwPhpError'), $this->action_result_container);

        $this->assertNull($controller->getServerParam($request, 'unknown'));
        $this->assertSame(123, $controller->getServerParam($request, 'unknown', 123));
    }
}
