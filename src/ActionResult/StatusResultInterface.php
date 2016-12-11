<?php

/*
 * This file is part of the Active Collab Controller project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Controller\ActionResult;

interface StatusResultInterface extends ActionResultInterface
{
    public function getStatusCode(): int;

    public function &setStatusCode(int $status_code): StatusResultInterface;

    public function getMessage(): string;

    public function &setMessage(string $message): StatusResultInterface;

    public function getPayload();

    public function &setPayload($payload): StatusResultInterface;
}
