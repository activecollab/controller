<?php

/*
 * This file is part of the Active Collab Controller project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\Controller\ActionResult\FileDownloadResult;

use ActiveCollab\Controller\ActionResult\ActionResultInterface;

interface FileDownloadResultInterface extends ActionResultInterface
{
    public function getFilePath(): string;

    public function getFileName(): string;

    public function getContentType(): string;

    public function isInline(): bool;

    public function getXType(): string;
}
