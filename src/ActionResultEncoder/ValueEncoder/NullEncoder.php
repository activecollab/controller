<?php

/*
 * This file is part of the Active Collab Controller project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Controller\ActionResultEncoder\ValueEncoder;

use Psr\Http\Message\ResponseInterface;

class NullEncoder implements ValueEncoderInterface
{
    use JsonContentTypeTrait;

    public function shouldEncode($value): bool
    {
        return $value === null;
    }

    /**
     * @param  ResponseInterface $response
     * @param  null              $value
     * @return ResponseInterface
     */
    public function encode(ResponseInterface $response, $value): ResponseInterface
    {
        return $this->setJsonContentType($response);
    }
}
