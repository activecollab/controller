<?php

/*
 * This file is part of the Active Collab Controller project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Controller\ActionResultEncoder\ValueEncoder;

use ActiveCollab\Controller\ActionResult\ViewResult\ViewResultInterface;
use ActiveCollab\Controller\ActionResultEncoder\ActionResultEncoderInterface;
use Psr\Http\Message\ResponseInterface;

class ViewEncoder extends ValueEncoder
{
    public function shouldEncode($value): bool
    {
        return $value instanceof ViewResultInterface;
    }

    /**
     * @param  ResponseInterface            $response
     * @param  ActionResultEncoderInterface $encoder
     * @param  ViewResultInterface          $value
     * @return ResponseInterface
     */
    public function encode(ResponseInterface $response, ActionResultEncoderInterface $encoder, $value): ResponseInterface
    {
        if ($value->getContentType() && $value->getEncoding()) {
            $response = $response->withHeader('Content-Type', $value->getContentType() . ';charset=' . $value->getEncoding());
        }

        return $response->withBody($this->createBodyFromText($value->fetch()));
    }
}
