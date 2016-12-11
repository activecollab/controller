<?php

/*
 * This file is part of the Active Collab Controller project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Controller\ActionResultEncoder\ValueEncoder;

use ActiveCollab\Controller\ActionResult\MovedResult\MovedResultInterface;
use ActiveCollab\Controller\ActionResultEncoder\ActionResultEncoderInterface;
use Psr\Http\Message\ResponseInterface;

class MovedResourceEncoder extends ValueEncoder
{
    use JsonContentTypeTrait;

    public function shouldEncode($value): bool
    {
        return $value instanceof MovedResultInterface;
    }

    /**
     * @param  ResponseInterface            $response
     * @param  ActionResultEncoderInterface $encoder
     * @param  MovedResultInterface         $value
     * @return ResponseInterface
     */
    public function encode(ResponseInterface $response, ActionResultEncoderInterface $encoder, $value): ResponseInterface
    {
        return $response
            ->withHeader('Location', $value->getUrl())
            ->withStatus($value->isMovedPermanently() ? 301 : 302);
    }
}
