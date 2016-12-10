<?php

/*
 * This file is part of the Active Collab Controller project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Controller\ActionResultEncoder\ValueEncoder;

use ActiveCollab\Controller\ActionResultEncoder\ActionResultEncoderInterface;
use ActiveCollab\Controller\ActionResult\FileDownloadResultInterface;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Stream;

class FileDownloadEncoder extends ValueEncoder
{
    public function shouldEncode($value): bool
    {
        return $value instanceof FileDownloadResultInterface;
    }

    /**
     * @param  ResponseInterface            $response
     * @param  ActionResultEncoderInterface $encoder
     * @param  FileDownloadResultInterface  $value
     * @return ResponseInterface
     */
    public function encode(ResponseInterface $response, ActionResultEncoderInterface $encoder, $value): ResponseInterface
    {
        if ($value->isInline()) {
            $content_type = $value->getContentType();
            $disposition = 'inline';
            $description = 'Binary';
        } else {
            $content_type = 'application/force-download';
            $disposition = 'attachment';
            $description = 'File Transfer';
        }

        $stream = new Stream(fopen($value->getFilePath(), 'rb'));

        /** @var ResponseInterface $response */
        $response = $response
            ->withHeader('Content-Type', $content_type)
            ->withHeader('Content-Description', $description)
            ->withHeader('Content-Transfer-Encoding', 'binary')
            ->withHeader('Content-Disposition', $disposition . '; filename=' . trim($value->getFileName()))
            ->withHeader('Content-Length', filesize($value->getFilePath()))
            ->withHeader('Expires', '0')
            ->withHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0')
            ->withHeader('Pragma', 'public');

        if ($value->getXType()) {
            $response = $response->withHeader('X-Type', $value->getXType());
        }

        return $response->withBody($stream);
    }
}
