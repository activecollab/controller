<?php

/*
 * This file is part of the Active Collab Controller project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Controller\ActionResultEncoder\ValueEncoder;

use ActiveCollab\Controller\Response\StatusResponseInterface;
use Psr\Http\Message\ResponseInterface;

class StatusEncoder extends ValueEncoder
{
    public function shouldEncode($value): bool
    {
        return $value instanceof StatusResponseInterface;
    }

    /**
     * @param  ResponseInterface       $response
     * @param  StatusResponseInterface $value
     * @return ResponseInterface
     */
    public function encode(ResponseInterface $response, $value): ResponseInterface
    {
        return $response->withStatus($value->getHttpCode(), $value->getMessage());
    }
}
