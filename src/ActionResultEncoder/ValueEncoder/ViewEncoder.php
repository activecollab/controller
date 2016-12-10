<?php

/*
 * This file is part of the Active Collab Controller project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Controller\ActionResultEncoder\ValueEncoder;

use ActiveCollab\Controller\Response\ViewResponseInterface;
use Psr\Http\Message\ResponseInterface;

class ViewEncoder extends ValueEncoder
{
    public function shouldEncode($value): bool
    {
        return $value instanceof ViewResponseInterface;
    }

    /**
     * @param  ResponseInterface     $response
     * @param  ViewResponseInterface $value
     * @return ResponseInterface
     */
    public function encode(ResponseInterface $response, $value): ResponseInterface
    {
        if ($value->getContentType() && $value->getEncoding()) {
            $response = $response->withHeader('Content-Type', $value->getContentType() . ';charset=' . $value->getEncoding());
        }

        return $response->withBody($this->createBodyFromText($value->fetch()));
    }
}
