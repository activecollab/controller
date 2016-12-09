<?php

/*
 * This file is part of the Active Collab Controller project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Controller\ActionResultEncoder\ValueEncoder;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

interface ValueEncoderInterface
{
    public function shouldEncode($value): bool;

    public function encode(ServerRequestInterface $request, ResponseInterface $response, $value): array;
}
