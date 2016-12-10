<?php

/*
 * This file is part of the Active Collab Controller project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Controller\ActionResultEncoder\ValueEncoder;

use ActiveCollab\Controller\ActionResultEncoder\ActionResultEncoderInterface;
use ActiveCollab\Controller\Response\StatusResponseInterface;
use Psr\Http\Message\ResponseInterface;

class StatusEncoder extends ValueEncoder
{
    public function shouldEncode($value): bool
    {
        return $value instanceof StatusResponseInterface;
    }

    /**
     * @param  ResponseInterface            $response
     * @param  ActionResultEncoderInterface $encoder
     * @param  StatusResponseInterface      $value
     * @return ResponseInterface
     */
    public function encode(ResponseInterface $response, ActionResultEncoderInterface $encoder, $value): ResponseInterface
    {
        $response = $response->withStatus($value->getHttpCode(), $value->getMessage());

        if ($value->getPayload()) {
            $response = $encoder->encode($response, $value->getPayload());
        }

        return $response;
    }
}
