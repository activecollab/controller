<?php

/*
 * This file is part of the Active Collab Controller project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\Controller\Response;

use RuntimeException;
use Slim\Http\Response;
use Slim\Http\Stream;

/**
 * @package ActiveCollab\Controller\Response
 */
class FileDownloadResponse implements ResponseInterface
{
    /**
     * @var string
     */
    private $file;

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

    /**
     * @param string      $file
     * @param string      $content_type
     * @param bool        $inline
     * @param string      $filename
     * @param string|null $x_type
     */
    public function __construct($file, $content_type, $inline = false, $filename = null, $x_type = null)
    {
        if (!is_file($file)) {
            throw new RuntimeException('Download file not found');
        }

        $this->file = $file;
        $this->content_type = $content_type;
        $this->inline = $inline;
        $this->filename = $filename;
        $this->x_type = $x_type;
    }

    public function createPsrResponse()
    {
        $filename = $this->filename ?: basename($this->file);
        $disposition = $this->inline ? 'inline' : 'attachment';

        $response = new Response();
        $stream = new Stream(fopen($this->file, 'rb'));

        /** @var Response $response */
        $response = $response
            ->withHeader('Content-Type', $this->inline ? $this->content_type : 'application/force-download')
            ->withHeader('Content-Description', $this->inline ? 'Binary' : 'File Transfer')
            ->withHeader('Content-Transfer-Encoding', 'binary')
            ->withHeader('Content-Disposition', $disposition . '; filename="' . $filename . '"')
            ->withHeader('Content-Length', filesize($this->file))
            ->withHeader('Expires', '0')
            ->withHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0')
            ->withHeader('Pragma', 'public');

        if ($this->x_type) {
            $response = $response->withHeader('X-Type', $this->x_type);
        }

        return $response->withBody($stream);
    }
}
