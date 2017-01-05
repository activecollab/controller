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
use ActiveCollab\Controller\ActionResultEncoder\ValueEncoder\ScalarEncoder;
use ActiveCollab\Controller\Test\Base\TestCase;
use ActiveCollab\Controller\Test\Fixtures\ActionResultInContainer;
use ActiveCollab\Controller\Test\Fixtures\ArrayAsJson;
use Pimple\Container;

class ScalarEncoderTest extends TestCase
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

    public function testShouldEncode()
    {
        $this->assertFalse((new ScalarEncoder())->shouldEncode(null));
        $this->assertFalse((new ScalarEncoder())->shouldEncode(new StatusResult(200)));
        $this->assertFalse((new ScalarEncoder())->shouldEncode([3, 2, 1]));
        $this->assertFalse((new ScalarEncoder())->shouldEncode(new ArrayAsJson([3, 2, 1])));
        $this->assertTrue((new ScalarEncoder())->shouldEncode('Test string'));
    }

    public function testEncodeScalar()
    {
        $response = $this->createResponse();

        $to_encode = 'Test string';

        $response = (new ScalarEncoder())->encode($response, new ActionResultEncoder($this->action_result_container), $to_encode);

        $response_body = (string) $response->getBody();

        $this->assertInternalType('string', $response_body);
        $this->assertSame('Test string', $response_body);
    }
}
