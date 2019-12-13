<?php

/*
 * This file is part of the Active Collab Controller project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Controller\ActionResult\StatusResult;

use ActiveCollab\Controller\ActionResult\ActionResultInterface;

interface StatusResultInterface extends ActionResultInterface
{
    public function getStatusCode(): int;
    public function getMessage(): string;
    public function getPayload();
}
