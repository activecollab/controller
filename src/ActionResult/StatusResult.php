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
    private $http_code;

    private $message;

    private $payload;

    public function __construct($http_code, $message = '', $payload = null)
    {
        if ($payload instanceof StatusResultInterface) {
            throw new LogicException('Status response is not an acceptible payload.');
        }

        $this->http_code = $http_code;
        $this->message = $message;
        $this->payload = $payload;
    }

    public function getHttpCode(): int
    {
        return $this->http_code;
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
