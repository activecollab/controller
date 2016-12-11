<?php

/*
 * This file is part of the Active Collab Controller project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Controller\ActionResultEncoder\ValueEncoder;

use ActiveCollab\Controller\ActionResultEncoder\ActionResultEncoderInterface;
use JsonSerializable;
use Psr\Http\Message\ResponseInterface;

class JsonSerializableEncoder extends ValueEncoder
{
    use JsonContentTypeTrait;

    public function shouldEncode($value): bool
    {
        return $value instanceof JsonSerializable;
    }

    /**
     * @param  ResponseInterface            $response
     * @param  ActionResultEncoderInterface $encoder
     * @param  JsonSerializable             $value
     * @return ResponseInterface
     */
    public function encode(ResponseInterface $response, ActionResultEncoderInterface $encoder, $value): ResponseInterface
    {
        $response = $this->setJsonContentType($response);

        return $response->withBody($this->createBodyFromText(json_encode($value)));
    }
}
