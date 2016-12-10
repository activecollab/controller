<?php

/*
 * This file is part of the Active Collab Controller project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Controller\ActionResult;

use InvalidArgumentException;
use RuntimeException;

class FileDownloadResult implements ActionResultInterface, FileDownloadResultInterface
{
    private $file_path;

    private $file_name;

    private $content_type;

    private $is_inline;

    private $x_type;

    public function __construct(string $file_path, string $content_type, bool $is_inline = false, string $file_name = '', string $x_type = '')
    {
        if (empty($file_path)) {
            throw new InvalidArgumentException('File path is required.');
        }

        if (!is_file($file_path)) {
            throw new RuntimeException('Download file not found.');
        }

        $this->file_path = $file_path;
        $this->content_type = $content_type;

        if (empty($this->content_type)) {
            $this->content_type = 'application/octet-stream';
        }

        $this->is_inline = $is_inline;
        $this->file_name = $file_name;

        if (empty($this->file_name)) {
            $this->file_name = basename($file_path);
        }

        $this->x_type = $x_type;
    }

    public function getFilePath(): string
    {
        return $this->file_path;
    }

    public function getFileName(): string
    {
        return $this->file_name;
    }

    public function getContentType(): string
    {
        return $this->content_type;
    }

    public function isInline(): bool
    {
        return $this->is_inline;
    }

    public function getXType(): string
    {
        return $this->x_type;
    }
}
