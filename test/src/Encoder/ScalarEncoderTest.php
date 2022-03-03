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
use LogicException;
use Pimple\Container;

class ScalarEncoderTest extends TestCase
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
        $this->assertFalse((new ScalarEncoder())->shouldEncode(null));
        $this->assertFalse((new ScalarEncoder())->shouldEncode(new StatusResult(200)));
        $this->assertFalse((new ScalarEncoder())->shouldEncode([3, 2, 1]));
        $this->assertFalse((new ScalarEncoder())->shouldEncode(new ArrayAsJson([3, 2, 1])));
        $this->assertTrue((new ScalarEncoder())->shouldEncode('Test string'));
    }

    /**
     * @dataProvider provideScalars
     * @param mixed $to_encode
     * @param string $expected_encoded_value
     */
    public function testEncodeScalar($to_encode, $expected_encoded_value)
    {
        $response = $this->createResponse();

        $response = (new ScalarEncoder())
            ->encode($response, new ActionResultEncoder($this->action_result_container), $to_encode);

        $response_body = (string) $response->getBody();

        $this->assertInternalType('string', $response_body);
        $this->assertSame($expected_encoded_value, $response_body);

        $decoded_body = json_decode($response_body, true);
        $this->assertInternalType(gettype($to_encode), $decoded_body);
    }

    public function provideScalars()
    {
        return [
            ['Test string', '"Test string"'],
            [1, '1'],
            [false, 'false'],
            [true, 'true'],
            [0.12, '0.12'],
            [1.0, '1.0'],
            [0.1234567890, '0.123457'],
        ];
    }

    /**
     * @dataProvider provideFloats
     * @param float  $to_encode
     * @param int    $float_precision
     * @param string $expected_encoded_value
     */
    public function testFloatPrecisionCanBeSpecified(float $to_encode, int $float_precision, string $expected_encoded_value)
    {
        $response = $this->createResponse();

        $response = (new ScalarEncoder($float_precision))
            ->encode($response, new ActionResultEncoder($this->action_result_container), $to_encode);

        $response_body = (string) $response->getBody();

        $this->assertInternalType('string', $response_body);
        $this->assertSame($expected_encoded_value, $response_body);
    }

    public function provideFloats()
    {
        return [
            [0.12, 6, '0.12'],
            [1.0, 6, '1.0'],
            [0.1234567890, 6, '0.123457'],
            [0.1234567890, 4, '0.1235'],
            [0.1234567890, 3, '0.123'],
        ];
    }

    public function testNonScalarValuesThrowException()
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage("Scalar encoder can encode only scalars.");

        $response = $this->createResponse();

        (new ScalarEncoder())->encode($response, new ActionResultEncoder($this->action_result_container), [1, 2, 3]);
    }
}
