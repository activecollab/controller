<?php

/*
 * This file is part of the Active Collab Controller project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Controller\Response;

interface ViewResponseInterface extends ResponseInterface
{
    public function getContentType(): string;

    public function getEncoding(): string;

    public function fetch(): string;
}
