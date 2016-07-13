<?php

/*
 * This file is part of the Active Collab Controller project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\Controller\Response;

use RuntimeException;

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

    /**
     * @return array
     */
    public function getHeaders()
    {
        $filename = $this->filename ?: basename($this->file);
        $disposition = $this->inline ? 'inline' : 'attachment';

        $result = [
            'Content-Description' => $this->inline ? 'Binary' : 'File Transfer',
            'Content-Type' => $this->inline ? $this->content_type : 'application/octet-stream',
            'Content-Length' => filesize($this->file),
            'Content-Disposition' => $disposition . ';filename="' . $filename . '"',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Pragma' => 'public',
        ];

        if ($this->x_type) {
            $result['X-Type'] = $this->x_type;
        }

        return $result;
    }

    public function loadFile()
    {
        readfile($this->file);
    }
}
