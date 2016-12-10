<?php

/*
 * This file is part of the Active Collab Controller project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Controller\Response;

interface StatusResponseInterface extends ResponseInterface
{
    public function getHttpCode(): int;

    public function getMessage(): string;

    public function getPayload();
}
