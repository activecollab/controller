<?php

/*
 * This file is part of the Active Collab Controller project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Controller\ActionResult\StatusResult;

use LogicException;

class StatusResult implements StatusResultInterface
{
    private $status_code;

    private $message;

    private $payload;

    public function __construct(
        int $status_code,
        string $message = '',
        $payload = null
    )
    {
        if ($payload instanceof StatusResultInterface) {
            throw new LogicException('Status response is not an acceptible payload.');
        }

        $this->status_code = $status_code;
        $this->message = $message;
        $this->payload = $payload;
    }

    public function getStatusCode(): int
    {
        return $this->status_code;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getPayload()
    {
        return $this->payload;
    }
}
