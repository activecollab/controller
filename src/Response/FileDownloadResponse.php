<?php

/*
 * This file is part of the Active Collab Controller project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Controller\Response;

use RuntimeException;
use Slim\Http\Response;
use Slim\Http\Stream;
use Psr\Http\Message\ResponseInterface as PsrResponseInterface;

class FileDownloadResponse implements ResponseInterface
{
    /**
     * @var string
     */
    private $file_path;

    /**
     * @var string
     */
    private $content_type;

    /**
     * @var bool
     */
    private $inline;

    /**
     * @var string
     */
    private $filename;

    /**
     * @var string|null
     */
    private $x_type;

    public function __construct(string $file_path, string $content_type, bool $inline = false, string $filename = null, string $x_type = null)
    {
        if (!is_file($file_path)) {
            throw new RuntimeException('Download file not found');
        }

        $this->file_path = $file_path;
        $this->content_type = $content_type;
        $this->inline = $inline;
        $this->filename = $filename;
        $this->x_type = $x_type;
    }

    public function createPsrResponse(PsrResponseInterface $response): PsrResponseInterface
    {
        $filename = $this->filename ?: basename($this->file_path);
        $disposition = $this->inline ? 'inline' : 'attachment';

        $stream = new Stream(fopen($this->file_path, 'rb'));

        /** @var Response $response */
        $response = $response
            ->withHeader('Content-Type', $this->inline ? $this->content_type : 'application/force-download')
            ->withHeader('Content-Description', $this->inline ? 'Binary' : 'File Transfer')
            ->withHeader('Content-Transfer-Encoding', 'binary')
            ->withHeader('Content-Disposition', $disposition . '; filename="' . $filename . '"')
            ->withHeader('Content-Length', filesize($this->file_path))
            ->withHeader('Expires', '0')
            ->withHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0')
            ->withHeader('Pragma', 'public');

        if ($this->x_type) {
            $response = $response->withHeader('X-Type', $this->x_type);
        }

        return $response->withBody($stream);
    }
}
