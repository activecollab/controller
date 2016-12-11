<?php

/*
 * This file is part of the Active Collab Controller project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Controller\ActionResult;

use LogicException;

class StatusResult implements StatusResultInterface
{
    private $status_code;

    private $message;

    private $payload;

    public function __construct(int $status_code, string $message = '', $payload = null)
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

    public function &setStatusCode(int $status_code): StatusResultInterface
    {
        $this->status_code = $status_code;

        return $this;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function &setMessage(string $message): StatusResultInterface
    {
        $this->message = $message;

        return $this;
    }

    public function getPayload()
    {
        return $this->payload;
    }

    public function &setPayload($payload): StatusResultInterface
    {
        $this->payload = $payload;
        return $this;
    }
}
