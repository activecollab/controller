<?php

/*
 * This file is part of the Active Collab Controller project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Controller\ActionResultEncoder\ValueEncoder;

use ActiveCollab\Controller\Response\FileDownloadResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class FileDownloadEncoder implements ValueEncoderInterface
{
    public function shouldEncode($value): bool
    {
        return $value instanceof FileDownloadResponse;
    }

    /**
     * @param  ServerRequestInterface $request
     * @param  ResponseInterface      $response
     * @param  FileDownloadResponse   $value
     * @return array
     */
    public function encode(ServerRequestInterface $request, ResponseInterface $response, $value): array
    {
        return [$request, $value->createPsrResponse($response)];
    }
}
