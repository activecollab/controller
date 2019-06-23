<?php

/*
 * This file is part of the Active Collab Controller project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Controller\ActionResultEncoder\ValueEncoder;

use ActiveCollab\Controller\ActionResultEncoder\ActionResultEncoderInterface;
use Psr\Http\Message\ResponseInterface;
use LogicException;

class ScalarEncoder extends ValueEncoder
{
    private $float_precision;

    public function __construct(int $float_precision = 6)
    {
        $this->float_precision = $float_precision;
    }

    public function shouldEncode($value): bool
    {
        return is_scalar($value);
    }

    /**
     * @param  ResponseInterface            $response
     * @param  ActionResultEncoderInterface $encoder
     * @param  string                       $value
     * @return ResponseInterface
     */
    public function encode(ResponseInterface $response, ActionResultEncoderInterface $encoder, $value): ResponseInterface
    {
        if (is_float($value)) {
            $value_to_encode = rtrim(number_format($value, $this->float_precision, '.', ''), '0');

            if (substr($value_to_encode, strlen($value_to_encode) - 1) === '.') {
                $value_to_encode .= '0';
            }
        } elseif (is_bool($value)) {
            $value_to_encode = $value ? 'true' : 'false';
        } elseif (is_int($value)) {
            $value_to_encode = (string) $value;
        } elseif (is_string($value)) {
            $value_to_encode = json_encode($value);
        } else {
            throw new LogicException('Scalar encoder can encode only scalars.');
        }

        return $response->withBody($this->createBodyFromText($value_to_encode));
    }
}
