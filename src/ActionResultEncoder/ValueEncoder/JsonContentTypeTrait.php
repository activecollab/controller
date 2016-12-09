<?php

/*
 * This file is part of the Active Collab Controller project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Controller\ActionResultEncoder\ValueEncoder;

use Psr\Http\Message\ResponseInterface;

trait JsonContentTypeTrait
{
    public function setJsonContentType(ResponseInterface $response): ResponseInterface
    {
        return $response->withHeader('Content-Type', 'application/json;charset=UTF-8');
    }
}
