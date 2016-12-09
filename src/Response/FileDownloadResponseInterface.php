<?php

/*
 * This file is part of the Active Collab Controller project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\Controller\Response;

interface FileDownloadResponseInterface extends ResponseInterface
{
    public function getFilePath(): string;

    public function getFileName(): string;

    public function getContentType(): string;

    public function isInline(): bool;

    public function getXType(): string;
}
